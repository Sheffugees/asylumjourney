FROM quay.io/continuouspipe/symfony-php7.2-nginx:stable
ARG GITHUB_TOKEN=
ARG SYMFONY_ENV=dev
ARG COMPOSER_INSTALL_FLAGS="--no-interaction --optimize-autoloader --no-scripts"

COPY . /app/
RUN container build


