<?php

/* app configuration */
Atomik::set(array(

	'atomik' => array(
		'dirs/plugins' => '../plugins/',
		'catch_errors' => true,
		'views/layout' => '_layout'
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
        'Session',
        'Lang',
        'Model',
        'Users',
        'Backend'
	)
    
));
