<?php

/* backend configuration */
config_merge(array(

	/* backend modules list
	 * The keys are the controllers name and the value
	 * must be an array with as first item the text
	 * that will appear in the menu and as second item
	 * the position in the menu
	 * Will be added in the same order */
	'backend_modules'				=> array(
	
		'dashboard'	=> array('Dashboard'		, 'left'),
		/*'content'	=> array('Content'			, 'left'),*/
		'pages' 	=> array('Pages'			, 'left'),
		
		'admin' 	=> array('Administration'	, 'right'),
		'doc' 		=> array('Documentation'	, 'right')
	
	),
	
	/* prefix to use for atomik tables */
	'backend_db_prefix'				=> 'atomik_',


	/* ----------------- resets the default configuration ---------------- */


	/* plugins used by the backend */
	'plugins' 						=> array('pdo', 'layout', 'session', 'lang', 'controller'),

	/* request */
	'core_default_action' 			=> 'index',
	
	/* error management */
	'core_handles_errors'			=> false,
	'core_display_errors'			=> true,

	/* paths */
	'core_paths_root'				=> './app/',
	'core_paths_plugins'			=> '../app/plugins/', /* uses the user app plugins */
	'core_paths_actions' 			=> './app/actions/',
	'core_paths_templates'	 		=> './app/templates/',
	'core_paths_includes'			=> './app/libraries/',

	/* filenames */
	'core_filenames_config' 		=> './app/config.php',
	'core_filenames_pre_dispatch' 	=> './app/pre_dispatch.php',
	'core_filenames_post_dispatch' 	=> './app/post_dispatch.php',
	'core_filenames_404' 			=> './app/404.php',
	'core_filenames_error' 			=> './app/error.php',
	
	
	/* ----------------- backend plugins configuration ---------------- */


	/* where to find user templates */
	'templates_dir'					=> '../app/templates',
	
	/* routes */
	'controller_routes'				=> array(
		'index' => array(
			'controller' => 'dashboard',
			'action' => 'index'
		)
	),
	
	/* database */
	'database'						=> true,
	'database_dsn'					=> 'mysql:host=localhost;dbname=atomik',
	'database_username'				=> 'atomik',
	'database_password'				=> 'atomik'

));

/* loads the Atomik_Backend class */
needed('Atomik/Backend');

