#!/bin/bash

get_version() {
  grep -E '^ \* Version:\s+(\d+\.\d+\.\d+)$' src/wordlift.php | grep -oE '(\d+\.\d+\.\d+)$'
}

if [[ 'src/readme.txt' == "$(git diff HEAD^ HEAD --name-only)" ]]; then
  echo 'readme.txt has been updated, pushing...'

  version="$(get_version)"
  sed -i '' "s|^Stable tag: .*$|Stable tag: $version|g" src/readme.txt

  # Update readme.txt in trunk and stable version tag.
  # shellcheck disable=SC2086
  svnmucc -u $SVN_USERNAME -p $SVN_PASSWORD --non-interactive --trust-server-cert \
    put src/readme.txt "https://plugins.svn.wordpress.org/wordlift/trunk/readme.txt" -m "Update readme.txt" \
    put src/readme.txt "https://plugins.svn.wordpress.org/wordlift/tags/$version/readme.txt" -m "Update readme.txt"
fi
