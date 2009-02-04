RELEASE_DIR = release

all: atomik plugins doc

atomik:
	mkdir -p $(RELEASE_DIR)
	svn export ./release-base release/atomik
	cp Atomik.php $(RELEASE_DIR)/atomik/index.php
	cp CHANGELOG.txt $(RELEASE_DIR)/atomik
	cp LICENSE.txt $(RELEASE_DIR)/atomik
	cd $(RELEASE_DIR) && zip -r atomik.zip atomik
	rm -r $(RELEASE_DIR)/atomik
	
plugins: plugin-controller plugin-db plugin-ajax plugin-cache plugin-console plugin-lang

plugin-controller:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Controller $(RELEASE_DIR)/plugins/Controller
	cd $(RELEASE_DIR)/plugins && zip -r atomik-plugin-controller.zip Controller
	rm -r $(RELEASE_DIR)/plugins/Controller

plugin-db:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Db $(RELEASE_DIR)/plugins/Db
	cd $(RELEASE_DIR)/plugins && zip -r atomik-plugin-db.zip Db
	rm -r $(RELEASE_DIR)/plugins/Db

plugin-ajax:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Ajax.php $(RELEASE_DIR)/plugins/Ajax.php
	cd $(RELEASE_DIR)/plugins && zip atomik-plugin-ajax.zip Ajax.php
	rm $(RELEASE_DIR)/plugins/Ajax.php

plugin-cache:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Cache.php $(RELEASE_DIR)/plugins/Cache.php
	cd $(RELEASE_DIR)/plugins && zip atomik-plugin-cache.zip Cache.php
	rm $(RELEASE_DIR)/plugins/Cache.php

plugin-console:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Console.php $(RELEASE_DIR)/plugins/Console.php
	cd $(RELEASE_DIR)/plugins && zip atomik-plugin-console.zip Console.php
	rm $(RELEASE_DIR)/plugins/Console.php

plugin-lang:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Lang.php $(RELEASE_DIR)/plugins/Lang.php
	cd $(RELEASE_DIR)/plugins && zip atomik-plugin-lang.zip Lang.php
	rm $(RELEASE_DIR)/plugins/Lang.php
	
doc:
	cd documentation && make -B OUTPUT=../$(RELEASE_DIR)/atomik-manual manual
	cd $(RELEASE_DIR) && zip -r atomik-manual.zip atomik-manual
	rm -r $(RELEASE_DIR)/atomik-manual
	
clean:
	rm -r $(RELEASE_DIR)