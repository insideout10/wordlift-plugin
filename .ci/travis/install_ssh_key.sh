#!/usr/bin/env bash

(
echo "Host gitlab.com"
echo "\tRSAAuthentication yes"
echo "\tUser git"
echo "\tIdentityFile $(pwd)/.ci/travis/id_rsa"
)> ~/.ssh/config

cat ~/.ssh/config
