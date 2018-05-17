# Contacts _(contacts)_

[![Master Build Status](https://travis-ci.org/alexanderglueck/contacts.svg?branch=master)](https://travis-ci.org/alexanderglueck/contacts)
[![StyleCI](https://styleci.io/repos/117006875/shield?branch=master)](https://styleci.io/repos/117006875)


> A simple contact management system to keep track of your friends and family. 

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
