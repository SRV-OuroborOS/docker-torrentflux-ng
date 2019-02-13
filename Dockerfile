FROM php:5.6-apache

COPY ./torrentflux /var/www/html/
RUN chown -R www-data:www-data /var/www/html

RUN apt-get update && apt-get install -y \
    mysql-client \
    wget \
    transmission-cli \
    uudeview \
    python \
    unzip \
    cksfv \
    net-tools \
    libpng-dev \
    unrar-free \
&& rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

RUN docker-php-ext-install mysql mysqli sockets gd

COPY ./configuration/init /init
COPY ./configuration/tf_php.ini /usr/local/etc/php/conf.d/

CMD php /init/init.php && apache2-foreground
