#!/bin/sh

GREEN_COLOR="\033[32m"
YELLOW_COLOR="\033[33m"
NORMAL_COLOR="\033[0m"

CURRENT_PWD=$(pwd)
cd "$(dirname $0)"

PLUGIN_NAME=$(basename $PWD)
VERSION=$(sed '/<version>/!d; s/^\s*<version>\([^<]*\)<\/version>.*$/\1/' infos.xml)

cd ..

CODEMIRROR="$(find ${PLUGIN_NAME} -type d -name 'codemirror*' | tail -n 1)"
CM_VERSION=$(echo ${CODEMIRROR} | sed -r 's/^.*-([0-9.]+)$/\1/')
ZIP_NAME="${CURRENT_PWD}/${PLUGIN_NAME}-$(echo ${VERSION} | sed 's/\./_/g').zip"
echo "

${YELLOW_COLOR}Plugin ${PLUGIN_NAME} : version ${VERSION}
Librairie Codemirror : version ${CM_VERSION}
Archive : ${ZIP_NAME}${NORMAL_COLOR}
"

zip -r -o ${ZIP_NAME}  \
	${PLUGIN_NAME}/*.php \
	${PLUGIN_NAME}/*.js \
	${PLUGIN_NAME}/*.xml \
	${PLUGIN_NAME}/*.sh \
	${PLUGIN_NAME}/*.html \
	${PLUGIN_NAME}/*.png \
	${PLUGIN_NAME}/.htaccess \
	${PLUGIN_NAME}/css/* \
	${PLUGIN_NAME}/lang/* \
	${PLUGIN_NAME}/tinymce/* \
	${CODEMIRROR}/lib/* \
	${CODEMIRROR}/addon/*/* \
	${CODEMIRROR}/mode/php/* \
	${CODEMIRROR}/mode/css/* \
	${CODEMIRROR}/mode/cmake/* \
	${CODEMIRROR}/mode/clike/clike.js \
	${CODEMIRROR}/mode/htmlembedded/* \
	${CODEMIRROR}/mode/htmlmixed/* \
	${CODEMIRROR}/mode/javascript/* \
	${CODEMIRROR}/mode/xml/* \
	${CODEMIRROR}/keymap/* \
	${CODEMIRROR}/theme/* \
	${CODEMIRROR}/demo/* \
	${CODEMIRROR}/doc/* \
	${CODEMIRROR}/LICENSE \
	${CODEMIRROR}/AUTHORS \
	${CODEMIRROR}/CHANGELOG.md \
	${CODEMIRROR}/README.md \
	${CODEMIRROR}/index.html \
	${CODEMIRROR}/package.json && \
echo "\nNom complet de l'archive du plugin:\n${GREEN_COLOR}${ZIP_NAME}${NORMAL_COLOR}\n"

cd $OLDPWD

echo "Done !"