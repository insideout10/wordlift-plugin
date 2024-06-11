#!/bin/bash

# shellcheck disable=SC1091

set -o errexit
set -o nounset
set -o pipefail

echo "** Running [.docker/wordpress/docker-entrypoint-init.d/10-wordpress-extra.sh] **"

# Enable DEBUG.
sed -i "s/'WP_DEBUG', false/'WP_DEBUG', true/g" /opt/bitnami/wordpress/wp-config.php

# sed -i "s/table_prefix = 'wp_'/table_prefix = 'wp_q81ara52nh_'/g" /opt/bitnami/wordpress/wp-config.php
# sed -i "s/table_prefix = 'wp_q81ara52nh_'/table_prefix = 'wp_'/g" /opt/bitnami/wordpress/wp-config.php

echo "** Set the rewrite structure **"
wp --skip-plugins --skip-themes rewrite structure '/%postname%/'

echo "** Installing and Activating plugins **"
wp plugin install woocommerce wordpress-seo wp-recipe-maker query-monitor wp-crontrol --force --activate || true

echo "** Creating wladmin user **"
# Check if the user exists
if $(wp user get wladmin >/dev/null 2>&1); then
    echo "** User wladmin already exists **"
else
    # Create the user
    wp user create wladmin wladmin@localdomain.localhost --role=administrator --user_pass=password
    echo "** User wladmin created **"
fi
