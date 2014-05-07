#!/bin/sh
. "setenv.sh"
./bin/install-wp-tests.sh $DB_NAME $DB_USERID $DB_PASSWORD $DB_HOST $WP_VERSION
phpunit $1 $2
