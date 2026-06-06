# Contacts _(contacts)_

> A simple contact management system to keep track of your friends and family.

Contacts allows you to manage your contacts by managing their/keeping track of
 - addresses
 - phone numbers
 - birthdays
 - recurring dates
 - email addresses
 - calls
 - notes
 - comments
 - websites
 - gift ideas

## Install

> Contacts uses [Docker](https://www.docker.com/) to keep the setup at a minimum.

1. Clone this repository and start the containers.
    ```bash
    git clone https://github.com/alexanderglueck/contacts.git
    cd contacts
    docker compose build
    docker compose up -d
    ```

2. The first time you start the containers, run the database migrations:
    ```bash
    docker exec -w /app contacts-app-1 php artisan migrate --seed
    ```

3. Visit `/install` to complete your installation.

4. Enjoy.

## Development

The `Dockerfile` is multi-stage. `docker compose build` produces the `dev`
target — php-fpm with xdebug and node, with the source bind-mounted from the
host. Other targets:

```bash
docker build --target production -t contacts:prod .
docker build --target testing    -t contacts:test .
```

Run any PHP-side command inside the app container so it can reach MySQL,
Redis, and Meilisearch on the compose network:

```bash
docker exec -w /app contacts-app-1 php artisan <command>
docker exec -w /app contacts-app-1 composer <command>
docker exec -w /app contacts-app-1 vendor/bin/phpunit
```

Front-end assets are built with Vite:

```bash
docker exec -w /app contacts-app-1 npm install
docker exec -w /app contacts-app-1 npm run dev    # HMR
docker exec -w /app contacts-app-1 npm run build  # production bundle
```

## Seeding the database

```bash
docker exec -it -w /app contacts-app-1 php artisan tinker
```
```php
Auth::loginUsingId(1);
config()->set('scout.prefix', 'tenant_1_');
\App\Models\Contact::factory()
    ->count(1000)
    ->create(['team_id' => 1, 'created_by' => 1, 'updated_by' => 1]);
```

## Scheduling & notifications

Birthday/important-date reminders are sent by the Laravel scheduler (defined in
`bootstrap/app.php`):

- `email:daily` — daily at **06:00 Europe/Vienna**, when there is at least one
  event today (birthday from `date_of_birth` or a `ContactDate`).
- `email:weekly` — Mondays at **06:00 Europe/Vienna**, when there is an event in
  the next two weeks.

Both honour each user's notification settings and send over **mail** and/or
**push** independently (`send_daily` / `send_daily_push`, `send_weekly` /
`send_weekly_push`).

The scheduler needs `schedule:run` invoked every minute. Add this to the host
crontab (or a cron sidecar container) **on the new system**:

```cron
* * * * * cd /app && php artisan schedule:run >> /dev/null 2>&1
```

### Cutover from `contacts-old`

When switching domains, to avoid double-sending reminders:

1. Disable the `schedule:run` cron on the **old** server (its schedule lives in
   `app/Console/Kernel.php`).
2. Enable the `schedule:run` cron on the **new** server (above).
3. Run the one-off birthday data migration once, after the contacts data is in
   place — it moves legacy `Geburtstag` `ContactDate` rows into
   `contacts.date_of_birth` and removes the source rows:

   ```bash
   php artisan contacts:migrate-birthdays --dry-run   # review first
   php artisan contacts:migrate-birthdays
   ```

### Push notifications (FCM)

Push uses `kreait/laravel-firebase` (Firebase Cloud Messaging). Set the service
account credentials in `.env` (see `config/firebase.php`):

```env
GOOGLE_APPLICATION_CREDENTIALS=/path/to/firebase-service-account.json
```

Devices register their FCM token via the API (`POST /api/v1/devices`); pushes are
sent at the lowest possible priority alongside the daily/weekly mails.

## Security

If you discover a security vulnerability within this application, please send an e-mail to Alexander Glück at security@alexanderglueck.at.
All security vulnerabilities will be promptly addressed.

Please do not open an issue describing the vulnerability.

## Maintainers

[@alexanderglueck][maintainer-alexanderglueck]

## Contribute

Feel free to dive in! Open an issue or submit PRs.

## License

See [LICENSE.md](LICENSE.md)

[maintainer-alexanderglueck]: https://github.com/alexanderglueck
