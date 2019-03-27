#!/bin/bash

#rm access.log error.log
rm master.zip
rm -f master.zip.1
#rm -r public_html/
rm -r convert-master/

wget -q https://github.com/JoKalliauer/convert/archive/master.zip -O master.zip
#wget -q https://codeload.github.com/toollabs/convert/zip/master -O master.zip
#wget -q https://codeload.github.com/JoKalliauer/convert/zip/2ca782cd2d52eb20d4ddf3f4a8d7d0e8517d7acc -O master.zip

unzip -oq master.zip

#mkdir ./public_html/
cp ./convert-master/* ./public_html
#mv -f ./convert-master/* ./public_html

#mv ./convert-master/ ./public_html


chmod a+x public_html/WorkaroundBotsvg2validsvg.sh public_html/webupdate.sh public_html/svg2base.sh public_html/keep.sh

cp public_html/webupdate.sh ./webupdate.sh

#chmod a+x ./webupdate.sh
