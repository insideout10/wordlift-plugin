#!/bin/bash

while [ -z "$IP" ]; do
  IP=$(curl -v nginx 2>&1 | grep -oP '([12]?[0-9]?[0-9]\.){3}[[12]?[0-9]?[0-9]' | uniq)
  sleep 1
done


echo "$IP    wordlift.localhost" >> /etc/hosts

exec docker-entrypoint.sh apache2-foreground
