FROM php:8.4-fpm-alpine

ARG USER_NAME
ARG UID
ARG GID

RUN apk add --no-cache \
    bash \
    git \
    curl \
    unzip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    mysql-client

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    bcmath \
    intl \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g ${GID} ${USER_NAME} \
    && adduser -D -u ${UID} -G ${USER_NAME} ${USER_NAME}

COPY www.conf /usr/local/etc/php-fpm.d/www.conf

RUN sed -i "s/__USER_NAME__/${USER_NAME}/g" \
    /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www

CMD ["php-fpm"]