<?php

config_merge(array(

	'plugins' => array(
		'console',
		'session',
		'pdo',
		'layout',
		'lang',
		'cache',
		'backend',
		'ajax'
	),
	
	/* database */
	'database'						=> true,
	'database_dsn'					=> 'mysql:host=localhost;dbname=atomik',
	'database_username'				=> 'atomik',
	'database_password'				=> 'atomik'

));
