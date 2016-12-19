#!/bin/sh

## See https://nodejs.org/en/download/package-manager/#debian-and-ubuntu-based-linux-distributions
#curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash -
#sudo apt-get install -y nodejs
#npm install -g npm

. $HOME/.nvm/nvm.sh
nvm install stable
nvm use stable
npm install