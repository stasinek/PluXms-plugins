#!/bin/sh

ICON_SIZE=32
FOLDER="mate-icon-theme-faenza/matefaenza/mimetypes/${ICON_SIZE}"
FILTRE="\
s/^.*mimetypes\/${ICON_SIZE}\/(gnome-mime-)?(.*)\.png\s+->\s+\.\/(.*)\.png$/\2\t\3/; \
/^(gnome-|gtk-|office|openoffice|lrwxrwxrwx)/d; \
/^[a-z]+-/!d; \
/^image-.*image-x-generic$/d; \
/^video-.*video-x-generic$/d \
"

git clone --depth 1 https://github.com/mate-desktop/mate-icon-theme-faenza.git
find ${FOLDER} -type f -name '*.png' -exec cp {} . \;
find ${FOLDER} -type l -name '*.png' -exec ls -l {} \; | sed -r "${FILTRE}" | sort  | uniq > substitutes.txt

cat >> substitutes.txt << EOT
text-calendar	vcalendar
text-csv	x-office-spreadsheet
application-vnd.debian.binary-package	package-x-generic
application/javascript	text-x-script
text-x-php	application-x-php
application-x-sql	text-x-sql
text-x-c++src.png                     	text-x-csrc
text-x-chdr	text-x-csrc
text-x-csharp	text-x-csrc
text-x-c++hdr	text-x-csrc
EOT

# copyright
cd mate-icon-theme-faenza
cp AUTHORS README COPYING NEWS ..
cd ..


