#!/bin/sh

npm install lightbox2 --save
mkdir lightbox2
mv node_modules/lightbox2/dist/* lightbox2/
rm -R node_modules
rm package-lock.json

