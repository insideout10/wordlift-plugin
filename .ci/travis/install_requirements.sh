#!/bin/sh

sudo apt-get update
# sudo apt-get install -y php5-gd
apt-get install -y curl php5-curl
# echo "extension=gd.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

php -i
