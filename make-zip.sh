#!/bin/sh

mkdir dist
cd dist

rm -fr wordlift-plugin wordlift-plugin-js wordlift

git clone -b master https://github.com/insideout10/wordlift-plugin.git
git clone -b master https://github.com/insideout10/wordlift-plugin-js.git

mkdir wordlift

cp -R wordlift-plugin/src/* wordlift/
cp -R wordlift-plugin-js/dist/latest/* wordlift/

rm -fr wordlift-plugin wordlift-plugin-js

output=`date "+wordlift-%Y%m%d-%H%M%S"`
zip -r $output wordlift

rm -fr wordlift-plugin wordlift-plugin-js wordlift
