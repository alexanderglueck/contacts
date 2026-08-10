#!/usr/bin/env sh
# Entrypoint for the production php-fpm container.
#
# WHY THIS EXISTS: this app's compose stack has no migration step and the image
# used to start php-fpm directly, so a deploy shipped new code against an
# unmigrated database — every schema change had to be remembered and applied by
# hand afterwards, with a window in between where the new code queried columns
# that did not exist yet. Running migrations here closes that window: the app
# serves no traffic until the schema matches the code it was built from. This is
# the same convention the other server-config apps use.
#
# Only the php-fpm service reaches this. compose.yml sets an explicit
# `entrypoint:` for contacts-init (chown) and contacts-scheduler (schedule:work),
# which overrides the image's entrypoint, so those containers never run
# migrations and cannot race this one.
#
# Deliberately NOT doing config/route/view caching here. This app has never run
# cached config in production, and turning it on as a side effect of a migration
# fix would change how every runtime env() call behaves. That is its own change.
set -eu

if [ "${SKIP_MIGRATIONS:-0}" = "1" ]; then
    echo "[entrypoint] SKIP_MIGRATIONS=1 — starting without running migrations."
else
    attempt=1
    max_attempts=30

    # The database lives outside this stack, so it may not be reachable the
    # instant we start. Retrying the migrate itself doubles as the wait: it
    # fails while the DB is unreachable and succeeds (as a no-op when there is
    # nothing pending) once it is up.
    until php artisan migrate --force --no-interaction; do
        if [ "${attempt}" -ge "${max_attempts}" ]; then
            echo "[entrypoint] migrations still failing after ${max_attempts} attempts — refusing to start." >&2
            echo "[entrypoint] the app would otherwise serve traffic against a schema it was not built for." >&2
            exit 1
        fi

        echo "[entrypoint] migrate failed (attempt ${attempt}/${max_attempts}) — database may still be starting; retrying in 2s."
        attempt=$((attempt + 1))
        sleep 2
    done
fi

# exec so php-fpm becomes PID 1 and receives Docker's stop signals directly.
exec "$@"
