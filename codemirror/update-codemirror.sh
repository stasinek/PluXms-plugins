#/bin/sh

# Where to download the last version of codemirror
URL="http://codemirror.net/codemirror.zip"

echo << EOT
This plugin uses requirejs
sudo npm i requirejs -g

EOT

TMP_FILE=$(mktemp --suffix .zip)

rm -f codemirror.zip
wget -O ${TMP_FILE} ${URL}
unzip ${TMP_FILE}
WORKING_DIR=$(find . -type d -name 'codemirror*' | sort | tail -n 1)
cd "${WORKING_DIR}"
npm install
version=$(jq -ra '.version' package.json)
echo -e "\e[33mversion ${version}\e[0m"
cd ..
rm ${TMP_FILE}

# update build.js with new version
# r.js -o build.js
r.js -o out=main.js name=../app baseUrl=${WORKING_DIR}

echo -e "\nDone\n"
