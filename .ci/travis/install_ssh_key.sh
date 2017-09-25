#!/usr/bin/env bash

(
echo "Host gitlab.com"
echo "  RSAAuthentication yes"
echo "  IdentityFile $HOME/.ci/travis/id_rsa"
)>> ~/.ssh/config

cat ~/.ssh/config
