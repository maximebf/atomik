<?php

/* app configuration */
Atomik::set(array(

	'atomik' => array(
		'dirs' => array(
			'plugins' => '../plugins/'
		)
	),

	'plugins' => array(

        'Console',

        'Db' => array(
            'autoconnect' => true,
            'dsn'         => 'mysql:host=localhost;dbname=atomik',
            'username'    => 'atomik',
            'password'    => 'atomik'
        ),
        
        'Backend' => array(

        	/* backend modules list
        	 * The keys are the controllers name and the value
        	 * must be an array with as first item the text
        	 * that will appear in the menu and as second item
        	 * the position in the menu
        	 * Will be added in the same order. However items
        	 * for the right position are added in reverse order */
        	'modules' => array(
        	
        		'dashboard'	=> array('Dashboard'		, 'left'),
        		/*'content'	=> array('Content'			, 'left'),*/
        		'pages' 	=> array('Pages'			, 'left'),
        		
        		'doc' 		=> array('Documentation'	, 'right'),
        		'admin' 	=> array('Administration'	, 'right'),
        		'users' 	=> array('Users'	        , 'right')
        	
        	),
    
        	/* where the backend is located */
        	'dir'		        => '../backend/',
        	
        	/* prefix to use for atomik tables */
        	'db_prefix'		    => 'atomik_',
        
        	/* where to find user templates */
        	'templates_dir'		=> '../example/app/templates'
        
        ),
        
        'Ajax',
		'Layout',
        'Session',
        'Lang'
	)
    
));
