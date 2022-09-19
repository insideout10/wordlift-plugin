#!/bin/bash

# shellcheck disable=SC1091

set -o errexit
set -o nounset
set -o pipefail

# Enable DEBUG.
sed -i "s/'WP_DEBUG', false/'WP_DEBUG', true/" /opt/bitnami/wordpress/wp-config.php

#echo "** Resetting URL **"
#wp --skip-plugins --skip-themes search-replace 'wordlift.io' 'store-wordlift-io.www.localhost'
#
#echo "** Set the rewrite structure **"
#wp --skip-plugins --skip-themes rewrite structure '/%postname%/'
#
#echo "** Enable plugins **"
#wp plugin activate advanced-custom-fields-pro woocommerce-eu-vat-number woocommerce-eu-vat-woofatture-bridge wp-redis
#
#echo "** Update WooCommerce database **"
#wp wc update

