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
            'dsn'         => 'mysql:host=localhost;dbname=atomik',
            'username'    => 'atomik',
            'password'    => 'atomik'
        ),
        
        'Config' => array(
        	'backend' => 'Xml',
        	'backend_args' => array('app/config.xml')
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
		'admin' => array('password' => 'admin', 'roles' => array('member', 'backend')),
		'foo' => array('password' => 'bar', 'roles' => array('member'))
	),
	'resources' => array(
		'/private/*' => 'member',
		'/backend/*' => 'backend'
	)
));
