<?php

config_merge(array(

	'plugins' => array(
		'console',
		'db',
		'layout',
		'cache',
		'session',
		'controller'
	),

	'database' 				=> true,
	'database_args' 		=> array('localhost', 'root', 'mysql*root'),
	'database_schema'		=> 'ronchon',
	
	'cache' 				=> false,
	'cache_requests' 		=> array(
		'index' => 20
	)

));
