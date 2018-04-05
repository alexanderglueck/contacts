# Contacts _(contacts)_

[![Master Build Status](https://travis-ci.org/alexanderglueck/contacts.svg?branch=master)](https://travis-ci.org/alexanderglueck/contacts)
[![StyleCI](https://styleci.io/repos/117006875/shield?branch=master)](https://styleci.io/repos/117006875)


> A simple contact management system to keep track of your friends and family. 

## Install

> To run this project, you must have PHP 7 installed as a prerequisite. 

Begin by cloning this repository to your machine, and installing all Composer & NPM dependencies.
Afterwards migrate and seed the database. 

```bash
git clone git@github.com:alexanderglueck/contacts.git
cd contacts && composer install && npm install
php artisan migrate --seed
npm run prod
```

## Security

If you discover a security vulnerability within this application, please send an e-mail to Alexander Glück at security@alexanderglueck.at. 
All security vulnerabilities will be promptly addressed.

Please do not open an issue describing the vulnerability. 

## Maintainers

[@alexanderglueck](https://github.com/alexanderglueck)

## Contribute

Feel free to dive in! Open an issue or submit PRs.

## License

See [LICENSE.md](LICENSE.md)
