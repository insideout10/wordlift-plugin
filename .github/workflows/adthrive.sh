#!/bin/bash

#sudo apt-get update -y && sudo apt-get install -y pcregrep

get_version() {
 pcregrep -o1 "Version.*([0-9]+\.[0-9]+\.[0-9]+)" src/wordlift.php
}

echo "packaging the plugin..."
cp src/ /tmp/wordlift -r
cp .github/workflows/adthrive.json /tmp/package.json
version="$(get_version)"

sed -i -r  's/Plugin Name:(..*)WordLift/Plugin Name:\1AdThrive SEO Amplifier – Powered by WordLift/' /tmp/wordlift/wordlift.php
sed -i -r  's#Description:(..*)#Description:        Automated schema enrichment powered by WordLift, exclusive to AdThrive publishers\n * Update URI:        https://adthrive.wordlift.io/seo/package.json#' /tmp/wordlift/wordlift.php

#
#
#cd /tmp && zip -r -D wordlift.zip wordlift/
#
#
#echo "updating the zip file..."
#curl --request PUT \
#  --url "https://adthrive.blob.core.windows.net/seo/wordlift.zip?$AZURE_STORAGE_SHARED_KEY_QUERY_PARAM" \
#  --header 'x-ms-blob-type: BlockBlob' \
#  --header 'x-ms-date: <date>' \
#  --data-binary "@/tmp/wordlift.zip" -m 300 -w '%{http_code}\n'
#
#
#echo "going to replace version $version in package.json"
#
#sed -i -r  "s/<version>/$version/" /tmp/package.json
#
#echo "updating the package.json file..."
#curl --request PUT \
#  --url "https://adthrive.blob.core.windows.net/seo/package.json?$AZURE_STORAGE_SHARED_KEY_QUERY_PARAM" \
#  --header 'x-ms-blob-type: BlockBlob' \
#  --header 'x-ms-date: <date>' \
#  --data-binary "@/tmp/package.json" -m 300 -w '%{http_code}\n'