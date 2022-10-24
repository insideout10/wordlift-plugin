#!/bin/bash

set -o errexit
set -o nounset
set -o pipefail

get_version() {
  grep -E '^ \* Version:\s+(\d+\.\d+\.\d+)$' src/wordlift.php | grep -oE '(\d+\.\d+\.\d+)$'
}

if [[ 'src/readme.txt' == $(git diff HEAD~1 --name-only) ]]; then
  echo 'readme.txt has been updated, pushing...'
fi

version="$(get_version)"
sed -i '' "s|^Stable tag: .*$|Stable tag: $version|g" src/readme.txt
git commit -m "update readme version" src/readme.txt
git push

git checkout svn
git checkout master -- src/readme.txt
mv src/readme.txt trunk/readme.txt
svn reset --hard
svn commit trunk/readme.txt
