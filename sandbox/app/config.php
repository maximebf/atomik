<?php

/* app configuration */
Atomik::set(array(

	'app' => array(
		'layout' => '_layout'
	),

	'atomik' => array(
		'url_rewriting' => true,
		'dirs' => array(
			'plugins' => array('../plugins/', '../laboratory/plugins'),
			'includes' => array('./app/includes/', './app/libraries/', '../library/')
		),
		'catch_errors' => true
	),

	'plugins' => array(

        'Console',

        'Db' => array(
            'dsn'         => 'mysql:host=localhost;dbname=atomik_sandbox',
            'username'    => 'atomik',
            'password'    => 'atomik'
        ),
        
        'Config',
        'Ajax',
        'Lang',
        'Auth',
        'Backend'
	)
    
));

Atomik::set('plugins/Auth', array(
	'model' => 'User',
	'users' => array(
		'admin' => array('password' => 'admin', 'roles' => array('backend', 'member'))
	),
	'resources' => array(
		'/private/*' => 'member'
	)
));
