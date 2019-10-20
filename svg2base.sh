#!/bin/bash
printf  "$(date) Zeile 02\n" >> foo.bar
#Author: Johannes Deml, Johannes Kalliauer
#Source: http://www.inkscapeforum.com/viewtopic.php?t=16743
#Download: http://ge.tt/7C8JFmF1/v/0?c
#Download date: 2017-10-29

#last Changes: (by Johannes Kalliauer)
#2017-10-29 11h06 defined Inkscape alias (Johannes Kalliauer)

echo


#Input parameters:
#alias inkscape='/cygdrive/c/Program\ Files/Inkscape/inkscape.com' #2017-10-29 11h06 (by Johannes Kalliauer)
#alias inkscape.exe='/cygdrive/c/Program\ Files/Inkscape/inkscape.exe'
#sourceType="base64"; valid=1
#outputType="svg"
#outputType="png"

~/.bash_profile

export PATH=/data/project/svgworkaroundbot/prgm2/OptiPNG/optipng-0.7.7/src/optipng:$PATH


#for fileSource in *.svg
#do

fileSource=$1

 export i=$fileSource #i will be overwritten later
 export fileN=$(echo $fileSource | cut -f1 -d" ") #remove spaces if exsiting (and everything after)
 export tmp=${fileN%.base64}

 #If you want to overwrite the existing file, without any backup, delete the following three lines
 export i=${tmp}.svg
 #if [ -f "$i" ]; then
 # echo no renaming
 #else
  echo move
  scour -i ${fileSource} -o $i --keep-unreferenced-defs --remove-descriptions --strip-xml-space  --set-precision=6 $META $INDENT --renderer-workaround --disable-style-to-xml  --set-c-precision=6 --protect-ids-noninkscape  --disable-simplify-colors  --keep-editor-data # --enable-comment-stripping --create-groups  #--enable-viewboxing # 
  cp $i $2 #debug
 #fi
 
 #mv "${fileSource}.$sourceType" "${fileSource}2.xml"
 echo $i
 if [ -f "$i" ]; then    
  count=$((count+1))
  file=${i%.base64}
  echo $count". "$i" -> "${file}.base64
  printf  "$(date) Zeile 50\n" >> foo.bar
  sed -ri "s/iVBORw0KGgoAAAANSUhEUgAA/ \niVBORw0KGgoAAAANSUhEUgAA/g" $i #linebreak before PNG
  sed -ri "s/\/9j\/4AAQSkZJRgABA(..)A(....)AAD\// \n\/9j\/4AAQSkZJRgABA\1A\2AAD\//g" $i #linebreak before JPG
  sed -ri "s/(AAAAAElFTkSuQmCC| QmCC|=)[ ]*\"(\/>| )/\1\n\"\2/g" $i #linebreak after end
  sed -ri "s/\r/ /" $i
  sed -ri "s/\n/ /" $i
  
  grep "iVBORw0KGgoAAAANSUhEUgAA" $i > $file.png_base64
  cp $file.png_base64 $2 #debug
  grep "\/9j\/4AAQSkZJRgABA..A....AAD\/"  $i > $file.jpeg_base64
  #printf  "$(date) Zeile 59\n" >> foo.bar
  linenumbers=$(wc -l $file.png_base64|awk '{print $1}')
  if [ "$linenumbers" -gt 0 ]; then
   if [ "$linenumbers" = 1 ]; then 
    sed -i "s/ /\n/g" $file.png_base64
    base64 --decode ${file}.png_base64 > ${file}.png
    cp ${file}.png $2 #debug
    pngfilesize=$(wc -c ${file}.png|awk '{print $1}')
    if [ "$pngfilesize" = "0" ];then
     rm ${file}.png
    fi
   else # [ "$linenumbers" = 1 ]
    for ((ln=1;ln<=$linenumbers;ln++))
	do
	 	echo $ln
     sed -n "${ln}p" $file.png_base64 > $file.png${ln}_base64
     sed -i "s/ /\n/g" $file.png${ln}_base64
     base64 --decode ${file}.png${ln}_base64 > ${file}_File${ln}.png
	done
    #sed -n '2p' $file.png_base64 > $file.png2_base64
    #sed -i "s/ /\n/g" $file.png2_base64
    #base64 --decode ${file}.png2_base64 > ${file}_File2.png
   fi #[ "$linenumbers" = 1 ]
  fi # [ "$linenumbers" -gt 0 ]
   printf  "$(date) Zeile 82\n" >> foo.bar
   sed -i "s/ /\n/g" $file.png_base64
   sed -i "s/ /\n/g" $file.jpeg_base64
   
   #base64.exe --decode ${file}.base64 > ${file}.png
   
   #if [ "$outputType" = "png" ];then #png
    base64 --decode ${file}.png_base64 > ${file}.png
	cp ${file}.png $2 #debug
   #elif [ "$outputType" = "jpeg" ] || [ "$outputType" = "jpg" ];then
    base64 --decode ${file}.jpeg_base64 > ${file}.jpeg
   #fi
   jpegfilesize=$(wc -c ${file}.jpeg|awk '{print $1}')
   pngfilesize=$(wc -c ${file}.png|awk '{print $1}')
   printf  "$(date) Zeile 95\n" >> foo.bar
   if [ "$jpegfilesize" = "0" ];then
    rm ${file}.jpeg
   else
    cp ${file}.jpeg $2
   fi
   if [ "$pngfilesize" = "0" ];then
    rm ${file}.png
   else
    optipng ${file}.png #maybe to slow for large files
    cp ${file}.png $2
   fi
   printf  "$(date) Zeile 107\n" >> foo.bar
   
  else
      echo "no file $i found!"
  fi
  
 if [ "$outputType" = "svg" ] || [ "$outputType" = "plain-svg" ] || [ "$outputType" = "ink-svg" ]; then
  mv ${i} ${file}bak2.xml
 fi
	
#done



echo "$count file(s) converted!"
printf  "$(date) Zeile 122\n" >> foo.bar
