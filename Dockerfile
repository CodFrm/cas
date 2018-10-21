FROM codfrm/nginx-php

LABEL maintainer="CodFrm <love@xloli.top>"

WORKDIR /var/www/html

ENV DB_HOST='127.0.0.1'\
    DB_USER='root'\
    DB_PASSWORD=''\
    DB_NAME='cas' \
    DB_PREFIX='cas_'\
    DB_PORT='3306'

RUN apk add --no-cache git \
    && git clone https://github.com/CodFrm/cas.git /var/www/html \
    && echo "DB_HOST=${DB_HOST}\nDB_USER=${DB_USER}\nDB_NAME=${DB_USER}\DB_PASSWORD=${DB_PASSWORD}\nDB_PREFIX=${DB_PREFIX}\nDB_PORT=${DB_PORT}" > .env \
    && rm index.html

CMD ["php","start.php"]