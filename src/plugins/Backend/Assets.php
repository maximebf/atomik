<?php

$assets->registerNamedAssets(array(

    'jquery' => array(
    	'externals/jquery-1.3.2.min.js'
    ),
    
    'jquery-ui' => array(
        '@jquery',
		'externals/jquery-ui/jquery-ui-1.7.1.custom.css',
		'externals/jquery-ui/jquery-ui-1.7.1.custom.min.js'
	),
	
	'Namespace.js' => array(
		'externals/Namespace.min.js'
	),
	
	'jwysiwyg' => array(
	    '@jquery',
		'externals/jwysiwyg/jquery.wysiwyg.css',
		'externals/jwysiwyg/jquery.wysiwyg.js'
	),
	
	'markitup' => array(
	    '@jquery',
		'externals/markitup/jquery.markitup.pack.js',
	    'externals/markitup/sets/default/set.js',
	    'externals/markitup/skins/markitup/style.css',
	    'externals/markitup/sets/default/style.css'
	),
	
	'dataTable' => array(
	    '@jquery',
	    'css/datatable.css',
	    'js/dataTable.js'
	),
	
	'autoComplete' => array(
	    '@jquery',
	    'externals/jquery.autocomplete.js',
	    'externals/jquery.autocomplete.css'
	),
	
	'common' => array(
	    '@jquery',
	    'css/main.css',
		'js/common.js'
	)
	
));

$assets->addNamedAsset('common');
