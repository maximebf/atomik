<?php

config_merge(array(

	'plugins' => array(
		'console',
		'session',
		'db',
		'layout',
		'lang',
		'cache',
		'cms',
		'ajax'
	),

	'database' 				=> true,
	'database_args' 		=> array('localhost', 'root', 'mysql*root'),
	'database_schema'		=> 'atomik',
	
	'cache' 				=> false,
	'cache_requests' 		=> array(
		'index' => 20
	),
	
	'layout_templates'		=> array(
		'index' => './templates/_index_layout.php'
	)

));
