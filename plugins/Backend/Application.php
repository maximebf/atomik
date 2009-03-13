<?php
	
	/* backend layout */
	Atomik::set('layout', 'main');
	
	/* default tabs */
	Atomik_Backend::addTab('Dashboard', 'Backend', 'index');
	
	$uri = Atomik::get('request_uri');
	if ($uri == 'index') {
		$uri = 'backend/index';
	}
	
	$segments = explode('/', trim($uri, '/'));
	$plugin = array_shift($segments);
	$uri = implode('/', $segments);
	
	Atomik::set('backend/plugin', $plugin);
	
	Atomik::fireEvent('Backend::Start');
	
    
    // dispatches the plugin application
    if (!Atomik::dispatchPluggableApplication($plugin, $uri, 'backend', null, false)) {
    	Atomik::trigger404();
    }

	return false;