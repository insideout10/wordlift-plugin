#!/bin/bash

# shellcheck disable=SC1091

set -o errexit
set -o nounset
set -o pipefail

# Enable DEBUG.
sed -i "s/'WP_DEBUG', false/'WP_DEBUG', true/g" /opt/bitnami/wordpress/wp-config.php

echo "** Set the rewrite structure **"
wp --skip-plugins --skip-themes rewrite structure '/%postname%/'


