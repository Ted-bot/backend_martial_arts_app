FROM php:8.3.11-fpm AS symfony_php

# persistent / runtime deps
RUN apt-get update && apt-get install -y \
		git \
        zip \
        ca-certificates \
        curl \
        lsb-release \
        gnupg

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer:2.1.65 /usr/bin/install-php-extensions /usr/local/bin/

RUN apt update \
    && install-php-extensions \
    intl \
    zip \
    apcu \
    pdo_pgsql \
    # imagick \
    gd \
    uuid \
    opcache \
    xdebug-^3.3

ARG IMAGICK_PHP83_FIX_COMMIT=9df92616f577e38625b96b7b903582a46c064739

RUN apt-get -q update \
    && apt-get -yq upgrade \
    && export PHP_DEV_DEPS='libmagickwand-dev' \
    && apt-get -yq install --no-install-recommends libmagickwand-6.q16-6 $PHP_DEV_DEPS \

    # Install Imagick from specific archive (PR's #616 code)
    && curl -L https://github.com/remicollet/imagick/archive/${IMAGICK_PHP83_FIX_COMMIT}.zip -o /tmp/imagick-issue-php83.zip \
    && unzip /tmp/imagick-issue-php83.zip -d /tmp \
    && pecl install /tmp/imagick-${IMAGICK_PHP83_FIX_COMMIT}/package.xml \

    && apt-get purge -y $PHP_DEV_DEPS $PHPIZE_DEPS \
    && apt-get autoremove -y --purge \
    && apt-get clean all \
    && rm -Rf /tmp/*

COPY /etc/docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY /etc/docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY /etc/docker/php/conf.d/99-php.ini $PHP_INI_DIR/conf.d/99-php.ini

COPY /etc/docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENV COMPOSER_MEMORY_LIMIT=-1 \
    COMPOSER_ALLOW_SUPERUSER=1 \
    PATH="${PATH}:/root/.composer/vendor/bin" \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=1 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=15000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=192 \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE=10 \
    PHP_OPCACHE_PRELOAD="" \
    PHP_EXPOSE_PHP="1"

# Install Node
RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg

ENV NODE_MAJOR=21
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list

RUN apt-get update && apt-get install nodejs -y

WORKDIR /app

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]