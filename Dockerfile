FROM composer/composer:php5-alpine
RUN docker-php-ext-install pdo_mysql
WORKDIR /srv/asylumjourney
EXPOSE 8000
ENTRYPOINT []
CMD ["sh"]
VOLUME /srv/asylumjourney
