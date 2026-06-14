# Contributing

Thanks for your interest in improving Contacts! This document explains how to
get a development environment running and what's expected of a contribution.

## Getting started

The project uses [Docker](https://www.docker.com/) so the setup stays minimal.

```bash
git clone https://github.com/alexanderglueck/contacts.git
cd contacts
docker compose build
docker compose up -d
docker exec -w /app contacts-app-1 php artisan migrate --seed
```

Run any PHP-, Composer- or Node-side command **inside the app container** so it
can reach MySQL, Redis and Meilisearch on the compose network:

```bash
docker exec -w /app contacts-app-1 php artisan <command>
docker exec -w /app contacts-app-1 composer <command>
docker exec -w /app contacts-app-1 npm install
docker exec -w /app contacts-app-1 npm run dev    # Vite HMR
```

See the [README](README.md) for more detail on the Docker targets and on
seeding data.

## Running the tests

```bash
docker exec -w /app contacts-app-1 vendor/bin/phpunit
```

Please add or update tests for any behaviour change, and make sure the full
suite passes before opening a pull request.

## Coding style

- PHP follows the Laravel/PSR-12 conventions. Match the style of the
  surrounding code.
- Respect the [.editorconfig](.editorconfig) settings (indentation, line
  endings, final newline).
- Keep changes focused — one logical change per pull request.

## Submitting changes

1. Fork the repository and create a topic branch off `develop`.
2. Make your change, with tests and a clear commit history.
3. Update `CHANGELOG.md` under the `Unreleased` heading when your change is
   user-facing.
4. Open a pull request against `develop` describing **what** changed and
   **why**.

## Reporting bugs and requesting features

Open a [GitHub issue](https://github.com/alexanderglueck/contacts/issues) with
enough detail to reproduce the problem (steps, expected vs. actual behaviour,
and relevant versions).

## Security

Please **do not** open public issues for security vulnerabilities. Email
Alexander Glück at security@alexanderglueck.at instead; see the Security
section of the [README](README.md).

## License

By contributing, you agree that your contributions will be licensed under the
project's [GNU AGPL-3.0-or-later](LICENSE.md) license.
