#!/bin/sh

echo Remember to call this script setting the correct WORDPRESS_VERSION environment variable.

docker cp ~/Downloads/advanced-custom-fields-pro.zip wordlift_wordpress_1:/var/www/html/wp-content/plugins/
#docker cp $(PWD)/bin/wordlift-how-to-acf.php wordlift_wordpress_1:/var/www/html/wp-content/plugins/
docker-compose run cli plugin install /var/www/html/wp-content/plugins/advanced-custom-fields-pro.zip --activate
#docker-compose run cli plugin activate wordlift-how-to-acf.php
