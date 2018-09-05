# Asylum Journey

Symfony API and admin backend for the Asylum Journey project.

The admin backend uses the Sonata admin bundle.

The API is JSON using HAL for HATEOS type stuff.

## API Documentation

API documentation can be found at http://docs.asylumjourney.apiary.io/#

### Editing the API Documentation

Changes to the API should be updated in the `apiary.apib` file. The editor at https://app.apiary.io/asylumjourney/editor
can be used to edit it. Apiary has not been connected to Github so the file will need to be manually copied into the editor
and copied back (or the integration between Github and Apiary set up)

### Testing the API Documentation

Dredd (https://github.com/apiaryio/dredd) can be used to test that the API matches the documentation.
This needs to be installed with npm:

```
npm install
```

and then run with

```
./node_modules/.bin/dredd
```

It runs the `AppBundle\Command\FixturesCommand` to set up the test data, so any additional data can be added there.

## Getting Started

There is a docker based local develop environment. This can be started with:

```
docker-compose up
```

An admin user for the admin panel can be created with:

```
docker-compose exec web /bin/bash
```

to get a bash session on the web container and that using that to run:

```
php app/console fos:user:create --super-admin
```

The JWT based authentication requires keys generating for encryption purposes.

This can be doe by running the following from the project root:

```
mkdir -p var/jwt 
openssl genrsa -out var/jwt/private.pem -aes256 4096
openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem
```

If you set a passphrase then this need to be updates in `app/config/parameters.yml`:

```
    jwt_key_pass_phrase: "the pass phrase"
```

Admin section can be found at `/admin`, log in with the details provided when running `fos:user:create`

## Heroku

Deploying code to Heroku can be done by pushing to the relevant git repo.

## Task list

https://trello.com/b/HCxgrmFQ/asylum-journey-task-list

