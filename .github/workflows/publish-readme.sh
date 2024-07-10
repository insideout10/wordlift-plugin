#!/bin/bash

get_version() {
  # This regex is crafted to work on ubuntu:22.04 which is the GH runner.
  grep -P '^ \* Version:\s+(\d+\.\d+\.\d+)$' src/wordlift.php | grep -oP '(\d+\.\d+\.\d+)$'
}

if [[ 'src/readme.txt' == "$(git diff HEAD^ HEAD --name-only)" || "$GITHUB_EVENT_NAME" == "workflow_dispatch" ]]; then
  echo 'readme.txt has been updated, pushing...'

  version="$(get_version)"
  echo "** Using version $version"
  sed -i "s|^Stable tag: .*$|Stable tag: $version|g" src/readme.txt

  echo "** readme.txt new contents: "
  cat src/readme.txt

  # Update readme.txt in trunk and stable version tag.
  # shellcheck disable=SC2086
  svnmucc -u $SVN_USERNAME -p $SVN_PASSWORD --non-interactive --trust-server-cert \
    put src/readme.txt "https://plugins.svn.wordpress.org/wordlift/trunk/readme.txt" -m "Update readme.txt" \
    put src/readme.txt "https://plugins.svn.wordpress.org/wordlift/tags/$version/readme.txt" -m "Update readme.txt"
fi
