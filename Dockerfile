FROM containerregistry.vietnampost.vn/ewallet-vnpd/php-fpm-8.1-nginx:v20220801
#RUN groupadd -g 1000 www
#RUN useradd -u 1000 -ms /bin/bash -g www www
#USER www
#COPY ./src /usr/share/nginx/sites
#RUN chown -R www /usr/share/nginx/sites
COPY ./src /usr/share/nginx/sites
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
#WORKDIR /usr/share/nginx/sites
#RUN composer install


ENTRYPOINT ["/etc/entrypoint.sh"]
