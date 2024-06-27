FROM laravelsail/php83-composer

RUN docker-php-ext-install pdo pdo_mysql
