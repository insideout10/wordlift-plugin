#!/bin/bash

FILE='src/wordlift.php'
README='trunk/readme.txt'

echo "Checking out and updating the svn branch..."
git checkout -b svn >/dev/null 2>&1
git pull origin svn >/dev/null 2>&1
echo "Updating the svn branch..."
svn up

echo "Checking out updated src..."
rm -fr src
git checkout main
git pull --all
git checkout svn
git checkout main -- src

VERSION=`grep -oP 'Version:\s+\K\d+\.\d+\.\d+' $FILE`

echo $VERSION

if [[ -z "$VERSION" ]]; then
	echo "Version not set, halting."
else
  echo "Removing tag $VERSION..."
  svn rm --force tags/$VERSION > /dev/null

	echo "If you see 'forbidden by the server', you need to authenticate to the server first."
	svn ci -m "$VERSION: updating trunk (1 of 2)"
	rsync -aP --delete src/ trunk/
	echo "Setting the stable tag in $README..."
	sed -i "s/Stable tag: .*/Stable tag: $VERSION/g" $README
	svn status | grep -E '^[?]' | awk '{print $2}' | xargs -r svn add
	svn status | grep -E '^[!]' | awk '{print $2}' | xargs -r svn delete
	svn cp trunk tags/$VERSION > /dev/null

	echo "If you see 'forbidden by the server', you need to authenticate to the server first."
	svn ci -m "$VERSION: updating trunk (2 of 2)"

	echo "Removing src..."
	rm -fr src

	git add -A
	git commit -m "bump to $VERSION" -a
	git push origin svn
fi
