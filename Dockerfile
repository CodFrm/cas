FROM codfrm/nginx-php

LABEL maintainer="CodFrm <love@xloli.top>"

WORKDIR /var/www

ENV DB_HOST='127.0.0.1'\
    DB_USER='root'\
    DB_PASSWORD=''\
    DB_NAME='cas' \
    DB_PREFIX='cas_'\
    DB_PORT='3306'

RUN apk add --no-cache git \
    && rm -rf html/ \
    && git clone https://github.com/CodFrm/cas.git /var/www/html \
    && cd html \
    && apk del git \
    && chown www .env

ENTRYPOINT php-fpm && nginx && cd html/ \
    && echo -e "DB_HOST=${DB_HOST}\nDB_USER=${DB_USER}\nDB_NAME=${DB_NAME}\nDB_PASSWORD=${DB_PASSWORD}\nDB_PREFIX=${DB_PREFIX}\nDB_PORT=${DB_PORT}" > .env \
    && php app/install.php \
    && php start.php
