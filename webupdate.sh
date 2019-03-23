#!/bin/bash

rm master.zip
rm -f master.zip.1
rm -r public_html/
rm -r convert-master/

wget -q https://github.com/JoKalliauer/convert/archive/master.zip -O master.zip

unzip -oq master.zip

mkdir ./public_html/

mv -f ./convert-master/* ./public_html

chmod a+x public_html/WorkaroundBotsvg2validsvg.sh

cp public_html/webupdate.sh ./webupdate.sh

chmod a+x ./webupdate.sh
