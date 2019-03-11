
rm master.zip
rm -r public_html/

wget https://github.com/JoKalliauer/convert/archive/master.zip

unzip -o master.zip

mv convert-master public_html

chmod a+x public_html/WorkaroundBotsvg2validsvg.sh

