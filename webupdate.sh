#!/bin/bash

rm master.zip
rm -f master.zip.1
rm -r public_html/

wget -q https://github.com/JoKalliauer/convert/archive/master.zip

unzip -oz master.zip

cp -Tr ./convert-master ./public_html

chmod a+x public_html/WorkaroundBotsvg2validsvg.sh

cp public_html/webupdate.sh ./webupdate.sh

chmod a+x ./webupdate.sh

