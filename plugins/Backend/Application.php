<?php

	// backend layout
	Atomik::set('app/layout', 'main');
	
	$uri = Atomik::get('request_uri');
	if (empty($uri)) {
		$uri = 'backend/index';
	}
	Atomik::fireEvent('Backend::Uri', array(&$uri));
	Atomik::set('backend/full_request_uri', $uri);
	
	// extracting the plugin name from the uri
	$segments = explode('/', trim($uri, '/'));
	$plugin = strtolower(array_shift($segments));
	$uri = implode('/', $segments);
	$baseAction = Atomik::get('atomik/base_action');
	
	if (empty($uri)) {
		$uri = 'index';
	}
	
	// reconfiguring
	Atomik::set('backend/plugin', $plugin);
	Atomik::set('backend/base_action', $baseAction);
	Atomik::set('atomik/base_action', $baseAction . '/' . $plugin);
	
	Atomik_Backend::addMenu('dashboard', 'Dashboard', 'backend');
	Atomik_Backend::addMenu('settings', 'Settings', 'backend/settings', array(), 'right');
	
	include dirname(__FILE__) . '/Assets.php';
	Atomik::fireEvent('Backend::Start', array($plugin));
	
	// configuration for the re-dispatch
	$pluggAppConfig = array(
		'pluginDir' 			=> null,
		'rootDir'				=> 'backend',
		'resetConfig'			=> false,
		'overwriteDirs'			=> false,
		'checkPluginIsLoaded' 	=> true
	);
	
	if ($plugin == 'app') {
		// this is the backend application for the user application, needs some reconfiguration
		// the backend dir is searched inside the app/ directory
		if (($pluggAppConfig['pluginDir'] = Atomik::path('backend', Atomik::get('atomik/dirs/app'))) === false) {
			throw new Exception('No backend application defined in your application');
		}
		$pluggAppConfig['rootDir'] = '';
		$pluggAppConfig['checkPluginIsLoaded'] = false;
	}
	
	// creates the __() function if it is not defined
	// this is to support i18n even if Lang is not loaded
	if (!function_exists('__')) {
		function __()
		{
	    	$args = func_get_args();
	    	return vsprintf(array_shift($args), $args);
		}
	}
	
	if (class_exists('Atomik_Form')) {
		Atomik_Form::setDefaultFormTemplate('Atomik/Backend/Form/FormTemplate.php');
		Atomik_Form::setDefaultFieldTemplate('Atomik/Backend/Form/FieldTemplate.php');
	}
    
    // dispatches the plugin application
    if (!Atomik::dispatchPluggableApplication($plugin, $uri, $pluggAppConfig)) {
    	Atomik::trigger404();
    }

    // to avoid dispatching the current application
	return false;