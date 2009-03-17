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
            'dsn'         => false, //'mysql:host=localhost;dbname=atomik',
            'username'    => '',
            'password'    => ''
        ),
        
        'Ajax',
        'Lang',
        'Auth' => array(),
        'Backend',
        'Pages'
	)
    
));

Atomik::set('plugins/Auth', array(
	'users' => array(
		'admin:admin' => array('member', 'backend'),
		'foo:bar' => array('member')
	),
	'resources' => array(
		'/private/*' => 'member',
		'/backend/*' => 'backend'
	)
));
