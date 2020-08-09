FROM php:7.4.1-fpm-alpine3.11

# composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer

RUN set -eux && \
apk update && \
apk add --update --no-cache  \
  freetype-dev \
  libpng-dev \
  libjpeg-turbo-dev && \
docker-php-ext-configure gd \
  --with-jpeg=/usr/include/ \
  --with-freetype=/usr/include/ && \
NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
docker-php-ext-install -j${NPROC} gd exif pdo_mysql && \
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer && \
curl -sSL https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64 -o mhsendmail \
    && chmod +x mhsendmail \
    && mv mhsendmail /usr/local/bin/mhsendmail
