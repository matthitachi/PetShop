FROM laravelsail/php83-composer

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www

COPY entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000
ENTRYPOINT ["entrypoint.sh"]
