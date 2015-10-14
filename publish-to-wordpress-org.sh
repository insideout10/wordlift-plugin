#!/bin/bash

FILE='src/wordlift.php'

echo "checking out and updating the svn branch..."
git checkout -b svn
git pull origin svn
echo "removing src..."
rm -fr src
echo "updating the svn branch..."
svn up
echo "checking out the src folder from master branch..."
git checkout master -- src

VERSION=`egrep -o "Version:\s+\d+\.\d+\.\d+" $FILE | egrep -o "\d+\.\d+\.\d+"`

if [[ -z "$VERSION" ]]; then
	echo "version not set, halting."
else
	echo "removig tag $VERSION..."
	svn rm --force tags/$VERSION
	echo "removig trunk..."
	svn rm --force trunk
	svn ci -m "updating trunk (1 of 2)"
	mv src trunk
	svn add trunk
	svn cp trunk tags/$VERSION
	svn ci -m "updating trunk (2 of 2)"

	git commit -m "bump to $VERSION" -a
	git push origin svn
fi


