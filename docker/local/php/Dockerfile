FROM php:8.2-fpm-alpine3.18

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN set -eux \
    && apk add --no-cache \
        icu-libs \
        libintl \
        make \
        gmp-dev \
        libzip-dev \
        zip

RUN set -eux \
    && apk add --no-cache freetype-dev libjpeg-turbo-dev libwebp-dev libpng-dev --virtual .build-deps \
        freetype \
		freetype-dev \
        autoconf \
        g++ \
        gcc \
        icu-dev \
        icu-libs \
        libwebp \
        libwebp-dev \
        libjpeg-turbo-dev \
        libjpeg-turbo \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-configure pdo_mysql --with-zlib-dir=/usr \
    && docker-php-ext-configure gd --enable-gd \
    && docker-php-ext-install -j$(nproc) bcmath \
    && docker-php-ext-install -j$(nproc) gmp \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN addgroup -g ${GID} --system laravel
RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel

COPY files/php.ini /usr/local/etc/php/php.ini
COPY files/php-fpm.conf /usr/local/etc/php-fpm.conf

WORKDIR /var/www

RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

RUN chown -R laravel:laravel /var/www
RUN chmod 755 /var/www

CMD ["php-fpm", "--nodaemonize"]
