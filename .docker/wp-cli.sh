#!/usr/bin/env bash

docker run --rm --volumes-from docker_wordlift-plugin-wordpress_1 --network container:docker_wordlift-plugin-wordpress_1 --user 33:33 wordpress:cli --path=/var/www/html "$@"
