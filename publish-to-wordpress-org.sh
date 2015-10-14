#!/bin/bash

FILE='wordlift/wordlift.php'

echo "checking out and updating the svn branch..."
git checkout -b svn
git pull origin svn
echo "removing make-zip.sh..."
rm -fr make-zip.sh
echo "updating the svn branch..."
svn up
echo "checking make-zip.sh from master branch..."
git checkout master -- make-zip.sh
echo "remove dist folder..."
rm -fr dist wordlift
echo "making wordlift.zip..."
./make-zip.sh master
echo "unzipping to wordlift/..."
unzip dist/wordlift-*.zip

VERSION=`egrep -o "Version:\s+\d+\.\d+\.\d+" $FILE | egrep -o "\d+\.\d+\.\d+"`

if [[ -z "$VERSION" ]]; then
	echo "version not set, halting."
else
	echo "removig tag $VERSION..."
	svn rm --force tags/$VERSION
	echo "removig trunk..."
	svn rm --force trunk
	svn ci -m "updating trunk (1 of 2)"
	mv wordlift trunk
	svn add trunk
	svn cp trunk tags/$VERSION
	svn ci -m "updating trunk (2 of 2)"

	git commit -m "bump to $VERSION" -a
	git push origin svn
fi


