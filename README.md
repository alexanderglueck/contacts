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
