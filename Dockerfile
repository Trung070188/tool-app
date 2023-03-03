FROM php:8.1-fpm
RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
        libzip-dev \
        libcurl4-openssl-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        zip \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql
RUN docker-php-ext-install pdo_mysql zip curl intl mbstring xml opcache
RUN pecl install redis && docker-php-ext-enable redis
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin
RUN mv /usr/local/bin/composer.phar /usr/local/bin/composer
#RUN groupadd -g 1000 www
#RUN useradd -u 1000 -ms /bin/bash -g www www
#USER www
COPY ./src /usr/share/nginx/sites
#RUN chown -R www /usr/share/nginx/sites
RUN apt-get update
RUN apt-get install -y nginx
RUN apt-get install -y git
COPY ./conf/nginx.conf /etc/nginx/nginx.conf
COPY ./conf/site.conf /etc/nginx/conf.d/site.conf
COPY ./conf/entrypoint.sh /etc/entrypoint.sh
COPY ./conf/schedule.sh /etc/schedule.sh
COPY ./conf/queue.sh /etc/queue.sh
COPY ./conf/php.ini /usr/local/etc/php/php.ini
RUN chmod +x /etc/entrypoint.sh
RUN chmod +x /etc/schedule.sh
RUN chmod +x /etc/queue.sh

#USER www
WORKDIR /usr/share/nginx/sites
RUN chmod -R 777 storage
RUN composer install



ENTRYPOINT ["/etc/entrypoint.sh"]
