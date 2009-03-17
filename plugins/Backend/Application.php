<?php
	
	/* backend layout */
	Atomik::set('app/layout', 'main');
	
	$uri = Atomik::get('request_uri');
	if (empty($uri)) {
		$uri = 'backend/index';
	}
	
	$segments = explode('/', trim($uri, '/'));
	$plugin = strtolower(array_shift($segments));
	$uri = implode('/', $segments);
	$baseAction = Atomik::get('atomik/base_action');
	
	if (empty($uri)) {
		$uri = 'index';
	}
	
	Atomik::set('backend/plugin', $plugin);
	Atomik::set('backend/base_action', $baseAction);
	Atomik::set('atomik/base_action', $baseAction . '/' . $plugin);
	Atomik::set('app/running_plugin', $plugin);
	
	/* default tabs */
	Atomik_Backend::addTab('Dashboard', 'Backend', 'index');
	
	Atomik::fireEvent('Backend::Start');
	
	$pluginDir = null;
	$rootDir = 'backend';
	$checkPluginIsLoaded = true;
	
	if ($plugin == 'app') {
		if (($pluginDir = Atomik::path('backend', Atomik::get('atomik/dirs/app'))) === false) {
			throw new Exception('No backend application defined in your application');
		}
		$rootDir = '';
		$checkPluginIsLoaded = false;
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
    
    // dispatches the plugin application
    if (!Atomik::dispatchPluggableApplication($plugin, $uri, $rootDir, $pluginDir, false, $checkPluginIsLoaded)) {
    	Atomik::trigger404();
    }

	return false;