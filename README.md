# Contacts _(contacts)_

[![Master Build Status][travis-image]][travis-url]
[![StyleCI][styleci-image]][styleci-url]

> A simple contact management system to keep track of your friends and family. 

Contacts allows you to manage your contacts by managing their 
 - addresses
 - phone numbers
 - birthdays

## Install

> To run this project, you must have PHP \>7 and MySQL \> 10.1.25 (MariaDB) installed as prerequisites. 

1. Create a mysql database for contacts (you will be asked for this database 
during the installation)
    ```mysql
    CREATE DATABASE your_database_name_here;
    ```

2. Continue by cloning this repository to your machine, and installing all Composer & NPM dependencies.
    ```bash
    git clone git@github.com:alexanderglueck/contacts.git
    cd contacts && composer install && npm install
    ``` 
 
3. Then run the contacts installer. 
    ```bash
    php artisan contacts:install
    ```

4. Finally compile the JavaScript and CSS assets
    ```bash
    npm run prod
    ```
    
5. Run contacts
    ```bash
    php artisan queue:listen
    php artisan serve
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

[travis-image]: https://travis-ci.org/alexanderglueck/contacts.svg?branch=master
[travis-url]: https://travis-ci.org/alexanderglueck/contacts

[styleci-image]: https://styleci.io/repos/117006875/shield?branch=master
[styleci-url]: https://styleci.io/repos/117006875

[maintainer-alexanderglueck]: https://github.com/alexanderglueck
