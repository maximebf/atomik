<?php

	// backend layout
	Atomik::set('app/layout', 'main');
	
	$uri = Atomik::get('request_uri');
	if (empty($uri)) {
		$uri = 'backend/index';
	}
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
	Atomik::set('app/running_plugin', $plugin);
	
	Atomik_Backend::addMenu('dashboard', 'Dashboard', 'backend');
	
	// needed libs
	Atomik_Backend_Assets::addScript('externals/jquery-1.3.2.min.js');
	Atomik_Backend_Assets::addStyle('externals/jquery-ui/jquery-ui-1.7.1.custom.css');
	Atomik_Backend_Assets::addScript('externals/jquery-ui/jquery-ui-1.7.1.custom.min.js');
	Atomik_Backend_Assets::addScript('externals/Namespace.min.js');
	
	Atomik_Backend_Assets::addStyle('css/main.css');
	Atomik_Backend_Assets::addStyle('css/form.css');
	
	// jwysiwyg
	Atomik_Assets::registerNamedAsset('jwysiwyg', array(
		Atomik_Backend_Assets::createAsset('externals/jwysiwyg/jquery.wysiwyg.css'),
		Atomik_Backend_Assets::createAsset('externals/jwysiwyg/jquery.wysiwyg.js')
	));
	
	// markitup
	Atomik_Assets::registerNamedAsset('markitup', array(
		Atomik_Backend_Assets::createAsset('externals/markitup/jquery.markitup.pack.js'),
		Atomik_Backend_Assets::createAsset('externals/markitup/sets/default/set.js'),
		Atomik_Backend_Assets::createAsset('externals/markitup/skins/markitup/style.css'),
		Atomik_Backend_Assets::createAsset('externals/markitup/sets/default/style.css')
	));
	
	Atomik::fireEvent('Backend::Start');
	
	// configuration for the re-dispatch
	$pluggAppConfig = array(
		'pluginDir' 			=> null,
		'rootDir'				=> 'backend',
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