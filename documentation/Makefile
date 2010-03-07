STYLESHEET = standalone
ATOMIK_BASE = ../..
LANG = en
OUTPUT = output/$(LANG)
XSL= xsl

all: manual getstarted events manifest

manuals: manual manual-framework manual-lib manual-plugins

manual:
	-mkdir -p $(OUTPUT)/manual
	cp $(XSL)/docbook/$(STYLESHEET).css $(OUTPUT)/manual/manual.css
	cat $(XSL)/docbook/common.css >> $(OUTPUT)/manual/manual.css
	svn export $(LANG)/manual/images $(OUTPUT)/manual/images
	xsltproc --xinclude \
		--stringparam atomik.base "$(ATOMIK_BASE)" \
		-o $(OUTPUT)/manual/ $(XSL)/docbook/$(STYLESHEET).xsl $(LANG)/manual/docbook/index.xml
	xsltproc --xinclude \
		--stringparam atomik.base "$(ATOMIK_BASE)/.." \
		--stringparam atomik.stylesheet "../manual.css" \
		-o $(OUTPUT)/manual/plugins/ $(XSL)/docbook/$(STYLESHEET).xsl $(LANG)/manual/docbook/plugins/index.xml
	xsltproc --xinclude \
		--stringparam atomik.base "$(ATOMIK_BASE)/.." \
		--stringparam atomik.stylesheet "../manual.css" \
		-o $(OUTPUT)/manual/library/ $(XSL)/docbook/$(STYLESHEET).xsl $(LANG)/manual/docbook/library/index.xml
	xsltproc --xinclude \
		--stringparam atomik.base "$(ATOMIK_BASE)/.." \
		--stringparam atomik.stylesheet "../manual.css" \
		-o $(OUTPUT)/manual/backend/ $(XSL)/docbook/$(STYLESHEET).xsl $(LANG)/manual/docbook/backend/index.xml

manual-framework:
	-mkdir -p $(OUTPUT)/manual-framework
	cp $(XSL)/docbook/$(STYLESHEET).css $(OUTPUT)/manual-framework/manual.css
	cat $(XSL)/docbook/common.css >> $(OUTPUT)/manual-framework/manual.css
	svn export $(LANG)/manual/images $(OUTPUT)/manual-framework/images
	xsltproc --xinclude \
		--stringparam atomik.base "$(ATOMIK_BASE)" \
		-o $(OUTPUT)/manual-framework/ $(XSL)/docbook/$(STYLESHEET).xsl $(LANG)/manual/docbook/index.xml
		
manual-lib:
	-mkdir -p $(OUTPUT)/manual-lib
	cp $(XSL)/docbook/$(STYLESHEET).css $(OUTPUT)/manual-lib/manual.css
	cat $(XSL)/docbook/common.css >> $(OUTPUT)/manual-lib/manual.css
	svn export $(LANG)/manual/images $(OUTPUT)/manual-lib/images
	xsltproc --xinclude \
		--stringparam atomik.base "$(ATOMIK_BASE)" \
		-o $(OUTPUT)/manual-lib/ $(XSL)/docbook/$(STYLESHEET).xsl $(LANG)/manual/docbook/library/index.xml
		
manual-plugins:
	-mkdir -p $(OUTPUT)/manual-plugins
	cp $(XSL)/docbook/$(STYLESHEET).css $(OUTPUT)/manual-plugins/manual.css
	cat $(XSL)/docbook/common.css >> $(OUTPUT)/manual-plugins/manual.css
	svn export $(LANG)/manual/images $(OUTPUT)/manual-plugins/images
	xsltproc --xinclude \
		--stringparam atomik.base "$(ATOMIK_BASE)" \
		-o $(OUTPUT)/manual-plugins/ $(XSL)/docbook/$(STYLESHEET).xsl $(LANG)/manual/docbook/plugins/index.xml
		
getstarted:
	-mkdir -p $(OUTPUT)/get-started
	mkdir $(OUTPUT)/get-started/images
	cp $(LANG)/manual/images/logo.png $(OUTPUT)/get-started/images
	cp $(XSL)/docbook/$(STYLESHEET).css $(OUTPUT)/get-started/manual.css
	cat $(XSL)/docbook/common.css >> $(OUTPUT)/get-started/manual.css
	xsltproc --xinclude \
		--stringparam atomik.base "$(ATOMIK_BASE)" \
		--stringparam atomik.menu "get-started" \
		-o $(OUTPUT)/get-started/ $(XSL)/docbook/$(STYLESHEET).xsl $(LANG)/get-started/index.xml
	svn export $(LANG)/get-started/source $(OUTPUT)/get-started/atomik-get-started
	svn export ../plugins/Db $(OUTPUT)/get-started/atomik-get-started/app/plugins/Db
	cp ../Atomik.php $(OUTPUT)/get-started/atomik-get-started/index.php
	cp ../CHANGELOG.txt $(OUTPUT)/get-started/atomik-get-started
	cp ../LICENSE.txt $(OUTPUT)/get-started/atomik-get-started
	cd $(OUTPUT)/get-started && zip -r atomik-get-started.zip atomik-get-started
	rm -r $(OUTPUT)/get-started/atomik-get-started
	
events: events-api
	-mkdir -p $(OUTPUT)/events
	cp $(XSL)/events/events.css $(OUTPUT)/events/events.css
	xsltproc --xinclude \
		--stringparam atomik.base "$(ATOMIK_BASE)" \
		-o $(OUTPUT)/events/ $(XSL)/events/events.xsl $(LANG)/events/index.xml
	
events-api:
	-mkdir -p $(OUTPUT)/events
	xsltproc --xinclude -o $(OUTPUT)/events/events-api.xml $(XSL)/events/events-api.xsl $(LANG)/events/index.xml
	
manifest:
	-mkdir -p $(OUTPUT)/manifest
	cp manifest.xsd $(OUTPUT)/manifest
		
clean:
	-rm -r $(OUTPUT)
