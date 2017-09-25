#!/usr/bin/env bash

# Protect the key file.
chmd 600 $(pwd)/.ci/travis/id_rsa

# Add the key file to the ssh config.
(
echo "Host gitlab.com"
echo "    RSAAuthentication yes"
echo "    User git"
echo "    IdentityFile $(pwd)/.ci/travis/id_rsa"
)> ~/.ssh/config

# Add gitlab to known hosts.
echo "gitlab.com,52.167.219.168 ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlzdHAyNTYAAAAIbmlzdHAyNTYAAABBBFSMqzJeV9rUzU4kWitGjeR4PWSa29SPqJ1fVkhtj3Hw9xjLVXVYrU9QlYWrOLXBpQ6KWjbjTDTdDkoohFzgbEY=" >> ~/.ssh/known_hosts

cat ~/.ssh/config
