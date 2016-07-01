# asylumjourney-frontend

Symfony API and admin backend for the Asylum Journey project.

## API Documentation

API documentation can be found at http://docs.asylumjourney.apiary.io/#

## Getting Started

Needs composer for package management, see installation instructions at https://getcomposer.org/doc/00-intro.md

There is no developer environment (e.g. Vagrant) at the moment, requires PHP 5.6+ and Mysql.
The Mysql username, password and database can be set in the `app/config/parameters.yml` file
(this file is not kept in version control so local parameters can be
 safely added to it)

```
git clone https://github.com/Sheffugees/asylumjourney

php composer.phar install

php app/console doctrine:schema:create

php app/console fos:user:create --super-admin
```

## Running locally

```
php app/console server:run
```

Admin section can be found at `/admin`, log in with the details provided when running `fos:user:create`

## Trello Import

Data can be imported from the Trello CSV export. This is intended for switching over from using Trello 
to enter data to using the admin panel rather than as an ongoing process. **The import deletes the existing
data.**

The CSV export needs to be in the root directory, named `trello.csv` and have the header line removed.
Then run:

```
php app/console app:csv-import
```

## Task list

https://trello.com/b/HCxgrmFQ/asylum-journey-task-list

