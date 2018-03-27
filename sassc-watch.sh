#!/bin/sh

echo "Rebuilding stylesheets..."
sassc -l -t expanded -m auto src/admin/css/wordlift-admin-setup.sass src/admin/css/wordlift-admin-setup.css

echo "Waiting for changes..."

fswatch -0 -i ".*\.sass$" -e ".*" src/admin/css | while IFS= read -r -d "" path
do

	echo "Rebuilding stylesheets... ($path changed)"
    sassc -l -t expanded -m auto src/admin/css/wordlift-admin-setup.sass src/admin/css/wordlift-admin-setup.css

done
