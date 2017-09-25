#!/usr/bin/env bash

(
echo "Host gitlab.com"
echo "    RSAAuthentication yes"
echo "    User git"
echo "    IdentityFile $(pwd)/.ci/travis/id_rsa"
)> ~/.ssh/config

echo "gitlab.com,52.167.219.168 ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlzdHAyNTYAAAAIbmlzdHAyNTYAAABBBFSMqzJeV9rUzU4kWitGjeR4PWSa29SPqJ1fVkhtj3Hw9xjLVXVYrU9QlYWrOLXBpQ6KWjbjTDTdDkoohFzgbEY=" >> ~/.ssh/known_hosts

cat ~/.ssh/config
