#!/bin/sh

set -e

DIST="/workspace/dependencies/php-laravel"

php-fpm -F -R -y $DIST/php-fpm.conf
