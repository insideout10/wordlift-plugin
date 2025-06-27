#!/bin/bash
set -eu

rm -fr wordlift wordlift-*.zip
rsync -av --exclude='*.map' --exclude='.git*' src/ wordlift/
version=$(grep "WORDLIFT_VERSION" src/wordlift.php | sed "s/.*'\([0-9.]*\)'.*/\1/")
zip_name="wordlift-${version}.zip"
zip -r -9 "$zip_name" wordlift
