<?php

/* app configuration */
Atomik::set(array(

	'atomik' => array(
		'dirs/plugins' => '../plugins/',
		'catch_errors' => true
	),

	'plugins' => array(

        'Console',

        'Db' => array(
            'autoconnect' => true,
            'dsn'         => 'mysql:host=localhost;dbname=atomik',
            'username'    => 'atomik',
            'password'    => 'atomik'
        ),
        
        'Ajax',
		'Layout',
        'Session',
        'Lang',
        'Model',
        'Users',
        'Backend'
	)
    
));
