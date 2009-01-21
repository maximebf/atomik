<?php

/* app configuration */
Atomik::set(array(

	'layout' => '_layout',

	'atomik' => array(
		'dirs/plugins' => '../plugins/',
		'catch_errors' => true
	),

	'plugins' => array(

        'Console',

        'Db' => array(
            'autoconnect' => false,
            'dsn'         => 'mysql:host=localhost;dbname=atomik',
            'username'    => 'atomik',
            'password'    => 'atomik'
        ),
        
        'Ajax',
        'Lang',
        'Model',
        'Users',
        'Backend'
	)
    
));
