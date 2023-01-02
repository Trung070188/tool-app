#!/bin/bash

QUEUE_ENABLED=$(php /usr/share/nginx/sites/artisan AppEnv QUEUE_ENABLED)

if [[ "$QUEUE_ENABLED" == "1" ]]; then
    while :
    do
      echo "queue:work started"
      php /usr/share/nginx/sites/artisan queue:work --tries=3 --timeout=120 2>&1
      sleep 5
    done
else
    echo "queue:work is disabled"
fi

