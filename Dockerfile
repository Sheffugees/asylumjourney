FROM quay.io/continuouspipe/symfony-php7.1-nginx:latest
ARG GITHUB_TOKEN=
ARG SYMFONY_ENV=dev
ARG DEVELOPMENT_MODE=true
ARG SYMFONY_WEB_APP_ENV_REWRITE=true
ARG APP_ENDPOINT=

COPY . /app/
RUN container build

