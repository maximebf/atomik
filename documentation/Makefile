OUTPUT = output
XSL = standalone

all: manual

manual:
	-mkdir -p $(OUTPUT)
	cp docbook-xsl/$(XSL).css $(OUTPUT)/manual.css
	cat docbook-xsl/common.css >> $(OUTPUT)/manual.css
	svn export manual/images $(OUTPUT)/images
	xsltproc --xinclude -o $(OUTPUT)/ docbook-xsl/$(XSL).xsl manual/docbook/index.xml
	xsltproc --xinclude \
		--stringparam atomik.base "../.." \
		--stringparam atomik.stylesheet "../manual.css" \
		-o $(OUTPUT)/plugins/ docbook-xsl/$(XSL).xsl manual/docbook/plugins/index.xml
		
get-started:
	-mkdir -p $(OUTPUT)
	cp docbook-xsl/$(XSL).css $(OUTPUT)/manual.css
	cat docbook-xsl/common.css >> $(OUTPUT)/manual.css
	xsltproc --xinclude -o $(OUTPUT)/ docbook-xsl/$(XSL).xsl get-started/index.xml
	
events:
	-mkdir -p $(OUTPUT)
	xsltproc --xinclude -o $(OUTPUT)/ events/xsl/events.xsl events/xml/index.xml
		
clean:
	-rm -r $(OUTPUT)