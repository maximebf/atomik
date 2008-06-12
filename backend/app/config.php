<?php

/* includes user config */
include ('../app/config.php');

/* resets default configuration */
Atomik::set(array(

    'atomik' => array(
    
    	/* request */
    	'trigger' 			    => 'action',
    	'default_action' 		=> 'index',
    	
    	/* error management */
    	'catch_errors'			=> false,
    	'display_errors'		=> true,
    
    	/* paths */
        'paths' => array(
        	'root'				=> './app/',
        	'plugins'			=> '../app/plugins/',
        	'actions' 			=> './app/actions/',
        	'templates'	 		=> './app/templates/',
        	'includes'			=> './app/libraries/'
        ),
    
    	/* filenames */
        'filenames' => array(
        	'config' 		    => './app/config.php',
        	'pre_dispatch' 	    => './app/pre_dispatch.php',
        	'post_dispatch' 	=> './app/post_dispatch.php',
        	'404' 			    => './app/404.php',
        	'error' 			=> './app/error.php'
        )
    	
    )
    
));

/* gets backend config */
if (Atomik::has('plugins/backend')) {
    Atomik::set('backend', Atomik::get('plugins/backend'));
} else if (Atomik::has('backend')) {
    Atomik::set('backend', Atomik::get('backend'));
}

/*  backend plugins configuration  */
Atomik::set('plugins', array(

    /* gets user configuration for the db */
    'db' => Atomik::get('plugins/db', array()),

	'layout', 
	'session', 
	'lang', 

	'controller' => array(
        /* routes */
		'routes'=> array(
    		'index' => array(
    			'controller' => 'dashboard',
    			'action' => 'index'
    		)
    	)
    	
	)
    	
));

/* loads the Atomik_Backend class */
Atomik::needed('Atomik/Backend');

