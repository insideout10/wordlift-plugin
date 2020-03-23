#!/bin/sh

cd ..
docker-compose up -d
docker-compose exec wordpress chown -R www-data:www-data .
docker cp ~/Downloads/advanced-custom-fields-pro.zip wordlift_wordpress_1:/var/www/html/wp-content/plugins/
docker-compose run cli plugin install /var/www/html/wp-content/plugins/advanced-custom-fields-pro.zip --activate
