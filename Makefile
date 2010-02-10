RELEASE_DIR = release

all: atomik library plugins doc

atomik:
	mkdir -p $(RELEASE_DIR)
	svn export ./release-base $(RELEASE_DIR)/atomik
	cp Atomik.php $(RELEASE_DIR)/atomik/index.php
	cp CHANGELOG.txt $(RELEASE_DIR)/atomik
	cp LICENSE.txt $(RELEASE_DIR)/atomik
	cd $(RELEASE_DIR) && zip -r atomik.zip atomik
	rm -r $(RELEASE_DIR)/atomik
	
library:
	mkdir -p $(RELEASE_DIR)
	svn export ./library $(RELEASE_DIR)/atomik-lib
	cp LICENSE.txt $(RELEASE_DIR)/atomik-lib
	cd $(RELEASE_DIR) && zip -r atomik-lib.zip atomik-lib
	rm -r $(RELEASE_DIR)/atomik-lib
	
plugins: plugin-controller plugin-db plugin-form plugin-ajax plugin-cache plugin-console plugin-lang plugin-auth plugin-backend plugin-config

plugin-controller:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Controller $(RELEASE_DIR)/plugins/Controller
	cd $(RELEASE_DIR)/plugins && zip -r atomik-plugin-controller.zip Controller
	rm -r $(RELEASE_DIR)/plugins/Controller

plugin-db:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Db $(RELEASE_DIR)/plugins/Db
	svn export library/Atomik/Db $(RELEASE_DIR)/plugins/Db/libraries/Atomik/Db
	svn export library/Atomik/Db.php $(RELEASE_DIR)/plugins/Db/libraries/Atomik/Db.php
	svn export library/Atomik/Model $(RELEASE_DIR)/plugins/Db/libraries/Atomik/Model
	svn export library/Atomik/Model.php $(RELEASE_DIR)/plugins/Db/libraries/Atomik/Model.php
	svn export library/Atomik/Options.php $(RELEASE_DIR)/plugins/Db/libraries/Atomik/Options.php
	cd $(RELEASE_DIR)/plugins && zip -r atomik-plugin-db.zip Db
	rm -r $(RELEASE_DIR)/plugins/Db

plugin-form:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Form $(RELEASE_DIR)/plugins/Form
	svn export library/Atomik/Form $(RELEASE_DIR)/plugins/Form/libraries/Atomik/Form
	svn export library/Atomik/Form.php $(RELEASE_DIR)/plugins/Form/libraries/Atomik/Form.php
	svn export library/Atomik/Options.php $(RELEASE_DIR)/plugins/Form/libraries/Atomik/Options.php
	svn export library/Atomik/Assets.php $(RELEASE_DIR)/plugins/Form/libraries/Atomik/Assets.php
	svn export library/Atomik/Assets $(RELEASE_DIR)/plugins/Form/libraries/Atomik/Assets
	cd $(RELEASE_DIR)/plugins && zip -r atomik-plugin-form.zip Form
	rm -r $(RELEASE_DIR)/plugins/Form

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
	
plugin-auth:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Auth $(RELEASE_DIR)/plugins/Auth
	svn export library/Atomik/Auth $(RELEASE_DIR)/plugins/Auth/libraries/Atomik/Auth
	svn export library/Atomik/Auth.php $(RELEASE_DIR)/plugins/Auth/libraries/Atomik/Auth.php
	cd $(RELEASE_DIR)/plugins && zip -r atomik-plugin-auth.zip Auth
	rm -r $(RELEASE_DIR)/plugins/Auth
	
plugin-backend:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Backend $(RELEASE_DIR)/plugins/Backend
	svn export library/Atomik/Assets.php $(RELEASE_DIR)/plugins/Backend/libraries/Atomik/Assets.php
	svn export library/Atomik/Assets $(RELEASE_DIR)/plugins/Backend/libraries/Atomik/Assets
	cd $(RELEASE_DIR)/plugins && zip -r atomik-plugin-backend.zip Backend
	rm -r $(RELEASE_DIR)/plugins/Backend
	
plugin-config:
	mkdir -p $(RELEASE_DIR)/plugins
	svn export plugins/Config $(RELEASE_DIR)/plugins/Config
	cd $(RELEASE_DIR)/plugins && zip -r atomik-plugin-config.zip Config
	rm -r $(RELEASE_DIR)/plugins/Config
	
doc: doc-complete doc-framework doc-lib doc-plugins

doc-complete:
	mkdir -p $(RELEASE_DIR)/doc
	cd documentation && make -B OUTPUT=../$(RELEASE_DIR)/doc manual
	cd $(RELEASE_DIR)/doc && mv manual atomik-manual && zip -r atomik-manual.zip atomik-manual
	rm -r $(RELEASE_DIR)/doc/atomik-manual
	
doc-framework:
	mkdir -p $(RELEASE_DIR)/doc
	cd documentation && make -B OUTPUT=../$(RELEASE_DIR)/doc manual-framework
	cd $(RELEASE_DIR)/doc && mv manual-framework atomik-framework-manual && zip -r atomik-framework-manual.zip atomik-framework-manual
	rm -r $(RELEASE_DIR)/doc/atomik-framework-manual

doc-lib:
	mkdir -p $(RELEASE_DIR)/doc
	cd documentation && make -B OUTPUT=../$(RELEASE_DIR)/doc manual-lib
	cd $(RELEASE_DIR)/doc && mv manual-lib atomik-lib-manual && zip -r atomik-lib-manual.zip atomik-lib-manual
	rm -r $(RELEASE_DIR)/doc/atomik-lib-manual
	
doc-plugins:
	mkdir -p $(RELEASE_DIR)/doc
	cd documentation && make -B OUTPUT=../$(RELEASE_DIR)/doc manual-plugins
	cd $(RELEASE_DIR)/doc && mv manual-plugins atomik-plugins-manual && zip -r atomik-plugins-manual.zip atomik-plugins-manual
	rm -r $(RELEASE_DIR)/doc/atomik-plugins-manual
	
clean:
	rm -r $(RELEASE_DIR)
