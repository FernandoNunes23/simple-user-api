FROM php:7-alpine

#INSTALANDO APCU
RUN apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && pecl clear-cache \
    && apk del .build-dependencies
