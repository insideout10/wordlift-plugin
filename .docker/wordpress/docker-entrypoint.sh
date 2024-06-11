#!/bin/bash

# shellcheck disable=SC1091

echo "** Booting up [.docker/wordpress/docker-entrypoint.sh] **"

while [ -z "$ipaddr" ]; do
  sleep 1
  ipaddr=$(getent hosts nginx | awk '{ print $1 }')
done

if [ ! -z "$ipaddr" ]; then
  echo "** Found nginx IP address $ipaddr **"

  echo "$ipaddr wordlift.localhost" >> /etc/hosts
  echo "$ipaddr wordlift.www.localhost" >> /etc/hosts
fi

set -o errexit
set -o nounset
set -o pipefail

if [ "$(stat -c "%U" /bitnami/wordpress/wp-content)" != "daemon" ]; then
  echo "** Resetting permissions **"
  chown -R daemon:root /bitnami
  find /bitnami -type d -print0 | xargs -0 chmod 755
fi

chmod 777 /dev/stderr
chmod 777 /dev/stdout

check_mysql_connection() {
    echo "SELECT 1" | mysql -s -h $WORDPRESS_DATABASE_HOST -u $WORDPRESS_DATABASE_USER -p$WORDPRESS_DATABASE_PASSWORD $WORDPRESS_DATABASE_NAME > /dev/null
}

while ! check_mysql_connection; do
  sleep 5
  echo "** Waiting for the database to come online"
done

echo ""
exec su -m -s /bin/bash -g root daemon -- "$@"
