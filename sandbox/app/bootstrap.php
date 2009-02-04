<?php

/* app configuration */
Atomik::set(array(

	'layout' => '_layout',

	'atomik' => array(
		'dirs/plugins' => array('../plugins/', '../laboratory/plugins'),
		'catch_errors' => true
	),

	'plugins' => array(

        'Console',

        'Db' => array(
            'dsn'         => false, //'mysql:host=localhost;dbname=atomik',
            'username'    => 'atomik',
            'password'    => 'atomik'
        ),
        
        'Ajax',
        'Lang',
        'Model',
        'Auth',
        'Backend',
        'Pages'
	)
    
));
