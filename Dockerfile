FROM composer:2.1.3 as build
RUN apk update \
    && apk add git curl libmcrypt-dev mysql-client libpng libpng-dev libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev gd
RUN docker-php-ext-install pdo pdo_mysql gd
WORKDIR /app
COPY . /app
RUN composer install --ignore-platform-reqs


FROM php:8.1-apache
RUN apt-get update \
    && apt-get install -yq git curl
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/


RUN chmod uga+x /usr/local/bin/install-php-extensions \
    && sync \
    && install-php-extensions pdo pdo_mysql gd gmp bcmath zip
EXPOSE 8080
COPY --from=build /app /var/www/
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
        sed -i -e "s/^ *memory_limit.*/memory_limit = 4G/g" /usr/local/etc/php/php.ini && \
    sed -i -e "s/^ *post_max_size.*/post_max_size = 15M/g" /usr/local/etc/php/php.ini && \
    sed -i -e "s/^ *upload_max_filesize.*/upload_max_filesize = 10M/g" /usr/local/etc/php/php.ini
RUN chmod 777 -R /var/www/storage/
RUN chmod +x /var/www/scripts/*.sh
RUN echo "Listen 8080" >> /etc/apache2/ports.conf
RUN chown -R www-data:www-data /var/www/ \
    && a2enmod rewrite
CMD ["/var/www/scripts/run.sh"]