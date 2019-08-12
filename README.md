# Contacts _(contacts)_

[![Master Build Status][travis-image]][travis-url]
[![StyleCI][styleci-image]][styleci-url]

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

> Contacts uses [Docker](https://www.docker.com/) to keep the setup at a minimum 

1. Begin by cloning this repository to your machine and starting the containers.
    ```bash
    git clone https://github.com/alexanderglueck/contacts.git
    cd contacts && docker-compose -f docker-compose.dist.yml build 
        && docker-compose -f docker-compose.dist.yml up -d
    ``` 

2. First time you start the containers, run the database migrations
    ```bash
    docker exec contacts_app_1 php artisan migrate --seed
   ```

3. Visit `/install` to complete your installation

4. Enjoy

## Setup
### Stripe
In order for contacts to work you need Stripe API tokens. 
Sign up for [Stripe] and enter your tokens into the `STRIPE_TOKEN` and 
`STRIPE_KEY` fields in your `.env` file. 

### Google Maps API Key
In order for maps in contacts to work you need a Google Maps API Key. 

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

[travis-image]: https://travis-ci.org/alexanderglueck/contacts.svg?branch=master
[travis-url]: https://travis-ci.org/alexanderglueck/contacts

[styleci-image]: https://styleci.io/repos/117006875/shield?branch=master
[styleci-url]: https://styleci.io/repos/117006875

[maintainer-alexanderglueck]: https://github.com/alexanderglueck

[Stripe]: https://stripe.com
