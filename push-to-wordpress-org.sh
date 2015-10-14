#!/bin/bash

FILE='wordlift/wordlift.php'
README='trunk/readme.txt'

echo "checking out and updating the svn branch..."
git checkout -b svn >/dev/null 2>&1
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
./make-zip.sh master > /dev/null
echo "unzipping to wordlift/..."
unzip dist/wordlift-*.zip > /dev/null

VERSION=`egrep -o "Version:\s+\d+\.\d+\.\d+" $FILE | egrep -o "\d+\.\d+\.\d+"`

if [[ -z "$VERSION" ]]; then
	echo "version not set, halting."
else
	echo "removing tag $VERSION..."
	svn rm --force tags/$VERSION > /dev/null
	echo "removing trunk..."
	svn rm --force trunk > /dev/null
	svn ci -m "updating trunk (1 of 2)"
	mv wordlift trunk
	echo "setting the stable tag in $README..."
	sed -i '' 's/Stable tag: .*/Stable tag: $VERSION/g' $README
	svn add trunk
	svn cp trunk tags/$VERSION
	svn ci -m "updating trunk (2 of 2)"

	git add -A
	git commit -m "bump to $VERSION" -a
	git push origin svn
fi


