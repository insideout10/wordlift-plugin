#!/usr/bin/env bash

(
echo "Host gitlab.com"
echo "    RSAAuthentication yes"
echo "    IdentityFile $(pwd)/.ci/travis/id_rsa"
)> ~/.ssh/config

cat ~/.ssh/config
