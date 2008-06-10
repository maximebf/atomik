<?php
	/**
	 * Atomik Framework
	 * A micro PHP Framework
	 * 
	 * @version 2.0
	 * @package Atomik
	 * @author 2008 (c) Maxime Bouroumeau-Fuseau
	 * @license http://www.opensource.org/licenses/mit-license.php
	 * @link http://pimpmycode.fr/atomik
	 */
	 
	define('ATOMIK_VERSION', '2.0');
	 
	/* the ONLY one global variable! */
	$_ATOMIK = array();
	
	/* -------------------------------------------------------------------------------------------
	 *  DEFAULT CONFIGURATION
	 * ------------------------------------------------------------------------------------------ */

	config_merge(array(

		/* plugins */
		'plugins'						=> array(),
	
		/* request */
		'core_action_trigger' 			=> 'action',
		'core_default_action' 			=> 'index',
		
		/* error management */
		'core_handles_errors'			=> false,
		'core_display_errors'			=> true,
	
		/* paths */
		'core_paths_root'				=> './',
		'core_paths_plugins'			=> './plugins/',
		'core_paths_actions' 			=> './actions/',
		'core_paths_templates'	 		=> './templates/',
		'core_paths_includes'			=> './includes/',
	
		/* filenames */
		'core_filenames_config' 		=> './config.php',
		'core_filenames_pre_dispatch' 	=> './pre_dispatch.php',
		'core_filenames_post_dispatch' 	=> './post_dispatch.php',
		'core_filenames_404' 			=> './404.php',
		'core_filenames_error' 			=> './error.php',
	
		'start_time' 					=> time() + microtime()
	));


	/* -------------------------------------------------------------------------------------------
	 *  CORE
	 * ------------------------------------------------------------------------------------------ */
	
	/* registers the error handler */
	if (config_get('core_handles_errors', true) === true) {
		set_error_handler('atomik_error_handler');
	}
	 
	/* loads external configuration */
	if (file_exists(config_get('core_filenames_config'))) {
		require(config_get('core_filenames_config'));
	}
	
	/* loads plugins */
	foreach (config_get('plugins') as $plugin) {
		load_plugin($plugin);
	}
	
	/* core is starting */
	events_fire('core_start');
	
	/* retreives the requested url and saves it into the configuration */
	if (!isset($_GET[config_get('core_action_trigger')]) || empty($_GET[config_get('core_action_trigger')])) {
		/* no trigger specified, using default page name */
		config_set('request', config_get('core_default_action'));
	} else {
		config_set('request', ltrim($_GET[config_get('core_action_trigger')], '/'));
		
		/* checking if no dot are in the page name to avoid any hack attempt and if no 
		 * underscore is use as first character in a segment */
		if (strpos(config_get('request'), '..') !== false || substr(config_get('request'), 0, 1) == '_' || 
			strpos(config_get('request'), '/_') !== false) {
				trigger404();
		}
	}
	
	/* all configuration has been set, ready to dispatch */
	events_fire('core_before_dispatch');
	
	/* global pre dispatch action */
	if (file_exists(config_get('core_filenames_pre_dispatch'))) {
		include(config_get('core_filenames_pre_dispatch'));
	}
	
	/* executes the action */
	if (atomik_execute_action(config_get('request'), true, true, false) === false) {
		trigger404();
	}
	
	/* dispatch done */
	events_fire('core_after_dispatch');
	
	/* global post dispatch action */
	if (file_exists(config_get('core_filenames_post_dispatch'))) {
		require(config_get('core_filenames_post_dispatch'));
	}
	
	/* end */
	atomik_end(true);
	
	
	/* -------------------------------------------------------------------------------------------
	 *  Core functions
	 * ------------------------------------------------------------------------------------------ */

	
	/**
	 * Executes an action
	 *
	 * @see atomik_render_template()
	 * @param string $action
	 * @param bool $render OPTIONAL (default true)
	 * @param bool $echo OPTIONAL (default false)
	 * @param bool $triggerError OPTIONAL (default true)
	 * @return array|string|bool
	 */
	function atomik_execute_action($action, $render = true, $echo = false, $triggerError = true)
	{
		$template = $action;
		$vars = array();
	
		events_fire('core_before_action', array(&$action, &$template, &$render, &$echo, &$triggerError));
		
		/* action and template filenames and existence */
		$actionFilename = config_get('core_paths_actions') . $action . '.php';
		$actionExists = file_exists($actionFilename);
		$templateFilename = config_get('core_paths_templates') . $template . '.php';
		$templateExists = file_exists($templateFilename);
		
		/* checks if at least the action file or the template file is defined */
		if (!$actionExists && !$templateExists) {
			if ($triggerError) {
				trigger_error('Action ' . $action . ' does not exists', E_USER_ERROR);
			}
			return false;
		}
	
		/* executes the action */
		if ($actionExists) {
			$vars = atomik_execute_action_scope($actionFilename);
			/* retreives the _render variable from the action scope */
			if (isset($vars['_render'])) {
				$render = $vars['_render'];
			}
			/* retreives the _template variable from the action scope */
			if (isset($vars['_template'])) {
				$template = $vars['_template'];
			}
		}
	
		events_fire('core_after_action', array($action, &$template, &$vars, &$render, &$echo, &$triggerError));
		
		/* returns $vars if the template is not rendered */
		if (!$render) {
			return $vars;
		}
		
		/* renders the template associated to the action */
		return atomik_render_template($template, $vars, $echo, $triggerError);
	}
	
	/**
	 * Requires the actions file inside a clean scope and returns defined
	 * variables
	 *
	 * @param string $__action_filename
	 * @return array
	 */
	function atomik_execute_action_scope($__action_filename)
	{
		global $_ATOMIK;
		require($__action_filename);
		
		/* retreives "public" variables (not prefixed with an underscore) */
		$definedVars = get_defined_vars();
		$vars = array();
		foreach ($definedVars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$vars[$name] = $value;
			}
		}
		
		return $vars;
	}
	
	/**
	 * Renders a template
	 *
	 * @param string $template
	 * @param array $vars OPTIONAL
	 * @param bool $echo OPTIONAL (default false)
	 * @param bool $triggerError OPTIONAL (default true)
	 * @return string|bool
	 */
	function atomik_render_template($template, $vars = array(), $echo = false, $triggerError = true)
	{
		events_fire('core_before_template', array(&$template, &$vars, &$echo, &$triggerError));
		
		/* template filename */
		$filename = config_get('core_paths_templates') . $template . '.php';
		
		/* checks if the file exists */
		if (!file_exists($filename)) {
			if ($triggerError) {
				trigger_error('Template ' . $filename . ' not found', E_USER_WARNING);
			}
			return false;
		}
		
		/* render the template in its own scope */
		$output = atomik_render_template_scope($filename, $vars);
		
		events_fire('core_after_template', array($template, &$output, &$vars, &$filename, &$echo, &$triggerError));
		
		/* checks if it's needed to echo the output */
		if (!$echo) {
			return $output;
		}
		
		/* echo output */
		events_fire('core_before_output', array($template, &$output));
		echo $output;
		events_fire('core_after_output', array($template, $output));
	}
	
	/**
	 * Renders a template in its own scope
	 *
	 * @param string $filename
	 * @param array $vars OPTIONAL
	 * @return string
	 */
	function atomik_render_template_scope($__template_filename, $vars = array())
	{
		extract($vars);
		ob_start();
		include($__template_filename);
		return ob_get_clean();
	}

	/**
	 * Fires the core_end event and exits the application
	 *
	 * @param bool $success OPTIONAL
	 */
	function atomik_end($success = false)
	{
		events_fire('core_end', array($success));
		exit;
	}
	
	/**
	 * Hanldes errors
	 *
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 * @param mixed $errcontext
	 */
	function atomik_error_handler($errno, $errstr, $errfile = '', $errline = 0, $errcontext = null)
	{
		/* handles errors depending on the level defined with error_reporting */
		if ($errno <= error_reporting()) {
			$args = func_get_args();
			events_fire('core_error', $args);
			
			/* checks if the user defined error file is available */
			if (file_exists(config_get('core_filenames_error'))) {
				include config_get('core_filenames_error');
				atomik_end(false);
			}
		
			echo '<h1>An error has occured!</h1>';
			
			/* only display error information if core_display_errors is sot to true */
			if (config_get('core_display_errors', true)) {
				echo '<p>' . $errstr . '</p><p>Code:' . $errno . '<br/>File: ' . $errfile .
				     '<br/>Line: ' . $errline . '</p>';
			}
		
			atomik_end(false);
		}
	}
	
	
	/* -------------------------------------------------------------------------------------------
	 *  Plugins functions
	 * ------------------------------------------------------------------------------------------ */

	
	/**
	 * Load a plugin
	 *
	 * @param string $plugin
	 * @param array $args OPTIONAL
	 */
	function load_plugin($plugin, $args = array())
	{
		global $_ATOMIK;
		
		/* initialize the plugins array */
		if (!isset($_ATOMIK['plugins'])) {
			$_ATOMIK['plugins'] = array();
		}
		
		/* checks if the plugin is already loaded */
		if (in_array($plugin, $_ATOMIK['plugins'])) {
			return;
		}
		
		events_fire('core_before_plugin', array(&$plugin));
		
		/* checks if plugin has been set to false from one of the event callbacks */
		if ($plugin === false) {
			return;
		}
		
		/* checks if the atomik_plugin_NAME function is defined */
		$pluginFunction = 'atomik_plugin_' . $plugin;
		if (!function_exists($pluginFunction)) {
			if (file_exists(config_get('core_paths_plugins') . $plugin . '.php')) {
				/* loads the plugin */
				require_once(config_get('core_paths_plugins') . $plugin . '.php');
			} else {
				/* plugin not found */
				trigger_error('Missing plugin: ' . $plugin, E_USER_WARNING);
				return;
			}
		}
		
		/* checks if the function atomik_plugin_NAME is defined. The use of this function
		 * is not mandatory in plugin file */
		if (function_exists($pluginFunction)) {
			call_user_func_array($pluginFunction, $args);
		}
		
		events_fire('core_after_plugin', array($plugin));
		
		/* stores the plugin name inside $_ATOMIK['plugins'] so we won't load it twice */
		$_ATOMIK['plugins'][] = $plugin;
	}
	
	/**
	 * Checks if a plugin is already loaded
	 *
	 * @param string $plugin
	 * @return bool
	 */
	function plugin_loaded($plugin)
	{
		global $_ATOMIK;
		
		/* initialize the plugins array */
		if (!isset($_ATOMIK['plugins'])) {
			$_ATOMIK['plugins'] = array();
		}
		
		return in_array($plugin, $_ATOMIK['plugins']);
	}
	
	
	/* -------------------------------------------------------------------------------------------
	 *  Config functions
	 * ------------------------------------------------------------------------------------------ */

	
	/**
	 * Merges current configuration with the array
	 *
	 * @param array $array
	 */
	function config_merge($array)
	{
		global $_ATOMIK;
		$_ATOMIK['config'] = array_merge(is_array($_ATOMIK['config']) ? 
									$_ATOMIK['config'] : array(), $array);
	}
	
	/**
	 * Gets a config value
	 *
	 * @param string $key
	 * @param mixed $default OPTIONAL Default value if the key is not found
	 * @return mixed
	 */
	function config_get($key, $default = '')
	{
		global $_ATOMIK;
		return array_key_exists($key, $_ATOMIK['config']) ? $_ATOMIK['config'][$key] : $default;
	}

	/**
	 * Sets a config key/value pair
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	function config_set($key, $value)
	{
		global $_ATOMIK;
		$_ATOMIK['config'][$key] = $value;
	}
	
	/**
	 * Like config merge but do not overwite
	 *
	 * @param array $values
	 */
	function config_set_default($values)
	{
		global $_ATOMIK;
		$_ATOMIK['config'] = array_merge($values, $_ATOMIK['config']);
	}
	
	/**
	 * Checks if a config key is defined
	 *
	 * @param string $key
	 * @return bool
	 */
	function config_isset($key)
	{
		global $_ATOMIK;
		return array_key_exists($key, $_ATOMIK['config']);
	}
	
	
	/* -------------------------------------------------------------------------------------------
	 *  Events functions
	 * ------------------------------------------------------------------------------------------ */

	
	/**
	 * Registers a callback to an event
	 *
	 * @param string $event
	 * @param callback $callback
	 */
	function events_register($event, $callback)
	{
		global $_ATOMIK;
		
		/* initialize the events array */
		if (!isset($_ATOMIK['events'])) {
			$_ATOMIK['events'] = array();
		}
		
		/* initialize the current event array */
		if (!isset($_ATOMIK['events'][$event])) {
			$_ATOMIK['events'][$event] = array();
		}
		
		/* stores the callback */
		$_ATOMIK['events'][$event][] = $callback;
	}
	
	/**
	 * Fires an event
	 * 
	 * @param string $event
	 * @param array $args OPTIONAL Arguments for callbacks
	 */
	function events_fire($event, $args = array())
	{
		global $_ATOMIK;
		
		if (isset($_ATOMIK['events'][$event])) {
			foreach ($_ATOMIK['events'][$event] as $callback) {
				call_user_func_array($callback, $args);
			}
		}
	}
	
	
	/* -------------------------------------------------------------------------------------------
	 *  Helper functions
	 * ------------------------------------------------------------------------------------------ */
	
	
	/**
	 * Triggers a 404 error
	 */
	function trigger404()
	{
		events_fire('404');
		
		/* HTTP header */
		header('HTTP/1.0 404 Not Found');
		
		if (file_exists(config_get('core_filenames_404'))) {
			/* includes the 404 error file */
			include(config_get('core_filenames_404'));
		} else {
			echo '<h1>404 - File not found</h1>';
		}
		
		atomik_end();
	}

	/**
	 * Redirects to another url
	 *
	 * @param string $destination
	 */
	function redirect($destination)
	{
		header('Location: ' . $destination);
		core_end();
	}

	/*
	 * Includes a file from the includes folder
	 * Do not specify the extension
	 *
	 * @param string $include
	 */
	function needed($include)
	{
		global $_ATOMIK;
		require_once(config_get('core_paths_includes') . $include . '.php');
	}
	
	/**
	 * Returns an url for the action
	 * TODO
	 *
	 * @param string $action
	 * @return string
	 */
	function get_url($action)
	{
		$url = 'index.php?' . config_get('core_action_trigger') . '=' . $action;
		
		$args = func_get_args();
		unset($args[0]);	
		events_fire('get_url', array($action, &$url, $args));
		
		return $url;
	}
	

	
