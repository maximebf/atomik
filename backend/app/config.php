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
    
    	/* dirs */
        'dirs' => array(
        	'root'				=> './app/',
        	'plugins'			=> '../app/plugins/',
        	'actions' 			=> './app/modules/',
        	'templates'	 		=> './app/templates/',
        	'includes'			=> './app/libraries/'
        ),
    
    	/* files */
        'files' => array(
        	'config' 		    => './app/config.php',
        	'pre_dispatch' 	    => './app/pre_dispatch.php',
        	'post_dispatch' 	=> './app/post_dispatch.php',
        	'404' 			    => './app/404.php',
        	'error' 			=> './app/error.php'
        )
    	
    ),
    
    'url_rewriting'				=> true
    
));

/*  backend plugins configuration  */
Atomik::set('plugins', array(

    /* gets user configuration for the db */
    'db' => Atomik::get('plugins/db', array()),

	'layout' => array(
        'global' => '_layout.php'
    ),
     
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
    	
	),

	'backend' => Atomik::get('plugins/backend', array())
	
));

Atomik::set('plugins/db/prefix', Atomik::get('plugins/backend/db_prefix', 'atomik_'));

/* loads the Atomik_Backend class */
Atomik::needed('Atomik/Backend');

