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
        	/*'plugins'			=> '../app/plugins/', */ // keep user configuration
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
    
    'url_rewriting' => true
    
));

/* saves user plugin configuration */
Atomik::set('user_app_plugins', Atomik::get('plugins'));

/*  backend plugins configuration  */
Atomik::set('plugins', array(

    /* gets user configuration for the db */
    'Db' => Atomik::get('plugins/Db', array()),

	'Layout' => array(
        'global' => '_layout.php'
    ),
     
	'Session', 
	'Lang', 

	'Controller' => array(
        /* routes */
		'routes'=> array(
            
    		'index' => array(
                'controller' => 'dashboard',
    			'action' => 'index'
    		)
    		
    	)
    	
	),

	'Backend' => Atomik::get('plugins/Backend', array())
	
));

Atomik::set('plugins/Db/prefix', Atomik::get('plugins/Backend/db_prefix', 'atomik_'));

/* loads the Atomik_Backend class */
Atomik::needed('Atomik/Backend');

