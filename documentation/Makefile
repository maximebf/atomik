OUTPUT = output
XSL = standalone

all: manual getstarted events manifest

manual:
	-mkdir -p $(OUTPUT)/manual
	cp docbook-xsl/$(XSL).css $(OUTPUT)/manual/manual.css
	cat docbook-xsl/common.css >> $(OUTPUT)/manual/manual.css
	svn export manual/images $(OUTPUT)/manual/images
	xsltproc --xinclude -o $(OUTPUT)/manual/ docbook-xsl/$(XSL).xsl manual/docbook/index.xml
	xsltproc --xinclude \
		--stringparam atomik.base "../.." \
		--stringparam atomik.stylesheet "../manual.css" \
		-o $(OUTPUT)/manual/plugins/ docbook-xsl/$(XSL).xsl manual/docbook/plugins/index.xml
	xsltproc --xinclude \
		--stringparam atomik.base "../.." \
		--stringparam atomik.stylesheet "../manual.css" \
		-o $(OUTPUT)/manual/library/ docbook-xsl/$(XSL).xsl manual/docbook/library/index.xml
	xsltproc --xinclude \
		--stringparam atomik.base "../.." \
		--stringparam atomik.stylesheet "../manual.css" \
		-o $(OUTPUT)/manual/backend/ docbook-xsl/$(XSL).xsl manual/docbook/backend/index.xml
		
getstarted:
	-mkdir -p $(OUTPUT)/get-started
	mkdir $(OUTPUT)/get-started/images
	cp manual/images/logo.png $(OUTPUT)/get-started/images
	cp docbook-xsl/$(XSL).css $(OUTPUT)/get-started/manual.css
	cat docbook-xsl/common.css >> $(OUTPUT)/get-started/manual.css
	xsltproc --xinclude -o $(OUTPUT)/get-started/ docbook-xsl/$(XSL).xsl get-started/index.xml
	
	svn export get-started/source $(OUTPUT)/get-started/atomik-get-started
	svn export ../plugins/Db $(OUTPUT)/get-started/atomik-get-started/app/plugins/Db
	cp ../Atomik.php $(OUTPUT)/get-started/atomik-get-started/index.php
	cp ../CHANGELOG.txt $(OUTPUT)/get-started/atomik-get-started
	cp ../LICENSE.txt $(OUTPUT)/get-started/atomik-get-started
	cd $(OUTPUT)/get-started && zip -r atomik-get-started.zip atomik-get-started
	rm -r $(OUTPUT)/get-started/atomik-get-started
	
events: events-api
	-mkdir -p $(OUTPUT)/events
	cp events/events.css $(OUTPUT)/events/events.css
	xsltproc --xinclude -o $(OUTPUT)/events/ events/events.xsl events/xml/index.xml
	
events-api:
	-mkdir -p $(OUTPUT)/events
	xsltproc --xinclude -o $(OUTPUT)/events/events-api.xml events/events-api.xsl events/xml/index.xml
	
manifest:
	-mkdir -p $(OUTPUT)/manifest
	cp manifest/manifest.xsd $(OUTPUT)/manifest
		
clean:
	-rm -r $(OUTPUT)