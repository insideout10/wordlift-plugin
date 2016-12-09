#!/bin/sh

mkdir htdocs
cd htdocs
wp core download
wp core config --dbname=wordpress --dbuser=root
wp core install --url=localhost --title=WordPress --admin_user=admin --admin_password=admin --admin_email=admin@example.org

# Finally link the WordLift plugin in WordPress.
ln -s ../src wp-content/plugins/wordlift
