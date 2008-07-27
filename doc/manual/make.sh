#!/bin/bash

MANUAL_LANG=$1
XSLFILE="/usr/share/xml/docbook/stylesheet/nwalsh/xhtml/chunk.xsl"

rm $MANUAL_LANG/output/*.html
rm $MANUAL_LANG/output/plugins/*.html
cp styles.css $MANUAL_LANG/output/

xsltproc --xinclude -o $MANUAL_LANG/output/ \
	--stringparam chunk.section.depth 0 \
	--stringparam chunker.output.indent yes \
	--stringparam html.stylesheet styles.css \
	$XSLFILE $MANUAL_LANG/index.xml

xsltproc --xinclude -o $MANUAL_LANG/output/plugins \
	--stringparam chunk.section.depth 0 \
	--stringparam chunker.output.indent yes \
	--stringparam html.stylesheet styles.css \
	$XSLFILE $MANUAL_LANG/plugins/index.xml
