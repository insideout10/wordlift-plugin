#!/bin/bash

get_version() {
  grep -E '^ \* Version:\s+(\d+\.\d+\.\d+)$' src/wordlift.php | grep -oE '(\d+\.\d+\.\d+)$'
}

echo "packaging the plugin..."
cp src/ /tmp/wordlift -r


sed -i -r  's/Plugin Name:(..*)WordLift/Plugin Name:\1AdThrive SEO Amplifier – Powered by WordLift/' /tmp/wordlift/wordlift.php
sed -i -r  's/Description:(..*)/Description: Automated schema enrichment powered by WordLift, exclusive to AdThrive publishers/' /tmp/wordlift/wordlift.php



zip -r /tmp/wordlift.zip /tmp/wordlift/


echo "updating the zip file..."
curl --request PUT \
  --url "https://adthrive.blob.core.windows.net/seo/wordlift.zip?$AZURE_STORAGE_SHARED_KEY_QUERY_PARAM" \
  --header 'x-ms-blob-type: BlockBlob' \
  --header 'x-ms-date: <date>' \
  --data-binary "@/tmp/wordlift.zip" -m 300 -w '%{http_code}\n'

cp .github/workflows/adthrive.json /tmp/package.json
version="$(get_version)"
echo "going to replace version $version in package.json"
sed -i -r  "s/<version>/$version/" /tmp/package.json

echo "updating the package.json file..."
curl --request PUT \
  --url "https://adthrive.blob.core.windows.net/seo/package.json?$AZURE_STORAGE_SHARED_KEY_QUERY_PARAM" \
  --header 'x-ms-blob-type: BlockBlob' \
  --header 'x-ms-date: <date>' \
  --data-binary "@/tmp/package.json" -m 300 -w '%{http_code}\n'