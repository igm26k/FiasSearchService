FROM tc-dockerhub.telecontact.ru/telecc-php/web:1.3-php7.1-apache-stretch

ARG ARG_APACHE_DOCUMENT_ROOT=/var/www/html/

ENV APACHE_DOCUMENT_ROOT=$ARG_APACHE_DOCUMENT_ROOT

RUN apt-get update \
    && apt-get install -y \
        libxml2-dev \
        wget \
        moreutils \
        unrar-free \
    && docker-php-ext-install soap \
    && pear config-set http_proxy "${http_proxy}" \
    && pecl install dbase

ADD docker/web/php.ini /usr/local/etc/php

COPY docker/web/bin/ /usr/local/bin/
COPY . $APACHE_DOCUMENT_ROOT

RUN echo "begin" \
    && cd $APACHE_DOCUMENT_ROOT \
    # && chmod 775 --recursive ./ \
    && echo $APACHE_DOCUMENT_ROOT \
    # && chown www-data:www-data --recursive ./ \
    && chown www-data:www-data  /var/www/html/vendor/autoload.php \
    && chown www-data:www-data  --recursive /var/www/html/vendor/composer/ \
    && chown www-data:www-data /var/www/html/secretsTemplate.json \
    && chown www-data:www-data --recursive /var/www/html/bootstrap/cache/ \
    && HOME=$APACHE_DOCUMENT_ROOT su \
        --login www-data \
        --shell /bin/bash \
        --preserve-environment \
        --command "cd /var/www/html && composer check-platform-reqs && composer install" \
    && cat composer.json | jq ".build = \"${BUILD_INFO}\"" | sponge composer.json \
    && chmod +x -R /usr/local/bin/ \
    && echo "done"

WORKDIR $APACHE_DOCUMENT_ROOT
