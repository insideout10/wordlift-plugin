#!/bin/bash

# shellcheck disable=SC1091

set -o errexit
set -o nounset
set -o pipefail

# Enable DEBUG.
sed -i "s/'WP_DEBUG', false/'WP_DEBUG', true/g" /opt/bitnami/wordpress/wp-config.php

# sed -i "s/table_prefix = 'wp_'/table_prefix = 'wp_q81ara52nh_'/g" /opt/bitnami/wordpress/wp-config.php
# sed -i "s/table_prefix = 'wp_q81ara52nh_'/table_prefix = 'wp_'/g" /opt/bitnami/wordpress/wp-config.php

echo "** Set the rewrite structure **"
wp --skip-plugins --skip-themes rewrite structure '/%postname%/'

echo "** Installing and Activating plugins **"
wp plugin install woocommerce wordpress-seo wp-recipe-maker --force --activate
