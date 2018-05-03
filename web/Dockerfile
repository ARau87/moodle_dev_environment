FROM php:7.0.29-apache

# Copy moodle and some config stuff to the right place
COPY config/php.ini /usr/local/etc/php/
COPY moodle/ /var/www/html/
COPY config/cron.sh /etc/cron.hourly/

# Installing moodle dependencies
RUN mkdir /etc/moodle &&  \
    chmod 2777 /etc/moodle/ \
    apt-get -y update && \
    apt-get -y upgrade && \
    apt-get -y install libpng-dev zlib1g-dev libicu-dev g++ && \
    docker-php-ext-configure intl && \
    docker-php-ext-install mysqli gd mbstring zip intl 





