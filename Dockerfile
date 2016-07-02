FROM composer/composer:php5-alpine
RUN docker-php-ext-install pdo_mysql
COPY . /srv/asylumjourney
WORKDIR /srv/asylumjourney
ENTRYPOINT []
CMD ["sh"]
