FROM quay.io/continuouspipe/symfony-php7.1-nginx:latest
ARG GITHUB_TOKEN=
ARG SYMFONY_ENV=dev
ARG DEVELOPMENT_MODE=true

COPY . /app/
RUN container build

