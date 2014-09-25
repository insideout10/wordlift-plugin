#!/bin/sh
. ./setenv.sh
echo $DB_NAME
./vendor/bin/phpunit $1 $2
