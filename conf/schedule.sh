#!/bin/bash
while :
do
	php /usr/share/nginx/sites/artisan schedule:run
	sleep 60
done
