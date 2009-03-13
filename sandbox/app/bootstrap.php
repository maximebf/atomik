<?php

/* app configuration */
Atomik::set(array(

	'layout' => '_layout',

	'url_rewriting' => true,

	'atomik' => array(
		'dirs/plugins' => array('../plugins/', '../laboratory/plugins'),
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
        'Model',
        'Auth' => array(),
        'Backend',
        'Pages'
	)
    
));

Atomik::set('plugins/Auth', array(
	'backend' => 'Array',
	'backend_args' => array(
		array(
			'maxime:toto' => array('member')
		)
	),
	
	'resources' => array(
		'/private/*' => 'member'
	),
	
	'guest_roles' => array(),
	'forbidden_action' => 'login'
));
