#!/usr/bin/env bash
/etc/schedule.sh &
/etc/queue.sh &
service nginx start
php-fpm
