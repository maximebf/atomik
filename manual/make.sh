#!/bin/bash

OUTPUT="output"
PROFILE="standalone"

if [ -n "$1" ]; then PROFILE=$1; fi
if [ -n "$2" ]; then OUTPUT=$2; fi

if [ ! -e $OUTPUT ]; then
	mkdir $OUTPUT;
else
	rm $OUTPUT/*.html
	rm $OUTPUT/plugins/*.html
	rm -r $OUTPUT/images
fi

cp stylesheets/style-$PROFILE.css $OUTPUT/manual.css
cat stylesheets/style-common.css >> $OUTPUT/manual.css

svn export images $OUTPUT/images

xsltproc --xinclude -o $OUTPUT/ stylesheets/docbook-$PROFILE.xsl docbook/index.xml
xsltproc --xinclude \
	--stringparam atomik.base "../.." \
	--stringparam atomik.stylesheet "../manual.css" \
	-o $OUTPUT/plugins/ stylesheets/docbook-$PROFILE.xsl docbook/plugins/index.xml
