<?php

// ----------------------------------------------------------------------------
// Libs
// ----------------------------------------------------------------------------

// jQuery
Atomik_Backend_Assets::registerNamedAsset('jquery', 'externals/jquery-1.3.2.min.js');

// jQuery UI
Atomik_Assets::registerNamedAsset('jquery-ui', array(
	Atomik_Backend_Assets::createAsset('externals/jquery-ui/jquery-ui-1.7.1.custom.css'),
	Atomik_Backend_Assets::createAsset('externals/jquery-ui/jquery-ui-1.7.1.custom.min.js', null, null, array('jquery'))
));

// Namespace.js
Atomik_Backend_Assets::registerNamedAsset('Namespace.js', 'externals/Namespace.min.js');

// jwysiwyg
Atomik_Assets::registerNamedAsset('jwysiwyg', array(
	Atomik_Backend_Assets::createAsset('externals/jwysiwyg/jquery.wysiwyg.css'),
	Atomik_Backend_Assets::createAsset('externals/jwysiwyg/jquery.wysiwyg.js', null, null, array('jquery'))
));

// markitup
Atomik_Assets::registerNamedAsset('markitup', array(
	Atomik_Backend_Assets::createAsset('externals/markitup/jquery.markitup.pack.js', null, null, array('jquery')),
	Atomik_Backend_Assets::createAsset('externals/markitup/sets/default/set.js'),
	Atomik_Backend_Assets::createAsset('externals/markitup/skins/markitup/style.css'),
	Atomik_Backend_Assets::createAsset('externals/markitup/sets/default/style.css')
));

// dataTable
Atomik_Assets::registerNamedAsset('dataTable', array(
	Atomik_Backend_Assets::createAsset('css/datatable.css'),
	Atomik_Backend_Assets::createAsset('js/dataTable.js', null, null, array('jquery'))
));

// auto complete
Atomik_Assets::registerNamedAsset('autoComplete', array(
	Atomik_Backend_Assets::createAsset('externals/jquery.autocomplete.js', null, null, array('jquery')),
	Atomik_Backend_Assets::createAsset('externals/jquery.autocomplete.css'),
));

// ----------------------------------------------------------------------------
// Themes
// ----------------------------------------------------------------------------

Atomik_Backend_Assets::addStyle('css/main.css');
Atomik_Backend_Assets::addScript('js/common.js', null, array('jquery'));