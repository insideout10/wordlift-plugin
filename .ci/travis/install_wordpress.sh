#!/bin/sh

mkdir htdocs
cd htdocs
# Add --version=<version> to specify which version to download.
# We should test at least with 4.5, 4.6, 4.7 and nightly test.
# See https://wp-cli.org/commands/core/download/
wp core download
wp core config --dbname=wordpress --dbuser=root
wp core install --url=localhost --title=WordPress --admin_user=admin --admin_password=admin --admin_email=admin@example.org

# Finally link the WordLift plugin in WordPress.
cd ..
ln -s "$(pwd)/src" htdocs/wp-content/plugins/wordlift
