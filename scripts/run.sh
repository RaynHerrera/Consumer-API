#!/bin/bash

php /var/www/artisan migrate

/usr/sbin/apachectl -D FOREGROUND
