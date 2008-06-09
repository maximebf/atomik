<?php
	/**
	 * COMPAT
	 *
	 * Ensure compatibility with versions older than 2.0
	 *
	 * @version 1.0
	 * @package Atomik
	 * @subpackage Compat
	 * @author Maxime Bouroumeau-Fuseau
	 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
	 * @license http://www.opensource.org/licenses/mit-license.php
	 * @link http://pimpmycode.fr/atomik
	 */
	 
	 /* checks if atomik's version is 2.0 or higher */
	 if (version_compare(ATOMIK_VERSION, '2.0') === -1) {
	 	return;
	 }

	/**
	 * Sets a new config key using an old one.
	 * Uses the new key value if the old key is not found
	 *
	 * @param string $oldKey
	 * @param string $newKey
	 */
	function compat_config_set_new($oldKey, $newKey)
	{
		config_set($newKey, config_get($oldKey, config_get($newKey)));
	}

	/**
	 * Sets an old key value with a new key value
	 *
	 * @param string $oldKey
	 * @param string $newKey
	 */
	function compat_config_set_old($oldKey, $newKey)
	{
		config_set($oldKey, config_get($newKey));
	}

	/* replaces new keys value with old keys value */
	compat_config_set_new('packages', 'plugins');
	compat_config_set_new('core_page_trigger', 'core_url_trigger');
	compat_config_set_new('core_default_page', 'core_default_action');
	compat_config_set_new('core_folder_packages', 'core_paths_plugins');
	compat_config_set_new('core_folder_logic', 'core_paths_actions');
	compat_config_set_new('core_folder_presentation', 'core_paths_templates');

	/**
	 * Handles the new core_before_dispatch event
	 */
	function compat_before_dispatch()
	{
		/* sets an old key value with the value of a new key */
		compat_config_set_old('current_page', 'request_url');
		compat_config_set_old('core_page_logic', 'request_action');
		compat_config_set_old('core_page_presentation', 'request_template');
		
		/* fires the old core_ready event */
		events_fire('core_ready');
	}
	events_register('core_before_dispatch', 'compat_before_dispatch');
	
	/**
	 * Handles the new core_before_action event
	 */
	function compat_before_action()
	{
		/* fires the old core_before_logic event */
		events_fire('core_before_logic');
	}
	events_register('core_before_action', 'compat_before_action');
	
	/**
	 * Handles the new core_after_action event
	 */
	function compat_after_action()
	{
		/* fires the old core_after_logic event */
		events_fire('core_after_logic');
	}
	events_register('core_after_action', 'compat_after_action');
	
	/**
	 * Handles the new core_after_template event
	 */
	function compat_after_template(&$output)
	{
		/* fires the old core_content_ready event */
		events_fire('core_content_ready', array(&$output));
	}
	events_register('core_after_template', 'compat_after_template');
	
	/**
	 * Handles the new core_before_output event
	 */
	function compat_before_output(&$output)
	{
		/* fires the old core_before_print event */
		events_fire('core_before_print', array(&$output));
	}
	events_register('core_before_output', 'compat_before_output');
	
	/**
	 * Handles the new core_after_output event
	 */
	function compat_after_output($output)
	{
		/* fires the old core_after_print event */
		events_fire('core_after_print', array($output));
	}
	events_register('core_after_output', 'compat_after_output');
	
	/**
	 * Recreates the old console_command event
	 */
	function compat_console($command, $arguments)
	{
		events_fire('console_command', array($command, $arguments));
	}
	
	/* adds console compatibility only if the console plugin is loaded */
	if (plugin_loaded('console')) {
		events_register('console_end', 'compat_console');
	}
	
	/**
	 * Emulates the old package function
	 * Adds support for old style package and new plugins
	 *
	 * @deprecated
	 * @param string $package
	 */
	function package($package)
	{
		global $_PLUGINS;
		if ($_PLUGINS === null) {
			$_PLUGINS = array();
		}
	
		/* checks if the package is already loaded */
		if (in_array($package, $_PLUGINS)) {
			return;
		}
		
		/* old and new functions name */
		$oldFunctionName = 'package_' . $package;
		$newFunctionName = 'atomik_plugin_' . $package;
	
		/* checks if package function exists */
		if(!function_exists($oldFunctionName) && !function_exists($newFunctionName)) {
			if(file_exists(config_get('core_paths_plugins') . $package . '.php')) {
				require_once(config_get('core_paths_plugins') . $package . '.php');
			} else {
				trigger_error('Missing package (compatibility mode): ' . $package, E_WARNING);
				return;
			}
		}

		/* fires both new and old events, the new one gets a second
		 * parameter to tell that we're calling in compat mode */
		events_fire('core_before_plugin', array($package, true));
		events_fire('core_before_package', array($package));
		
		/* executes the function */
		if (function_exists($oldFunctionName)) {
			call_user_func_array($oldFunctionName, $args);
		} else if (function_exists($newFunctionName)) {
			call_user_func_array($newFunctionName, $args);
		}
		
		/* same as before */
		events_fire('core_after_package', array($package));
		events_fire('core_after_plugin', array($package, true));
	
		$_PLUGINS[] = $package;
	}

	/**
	 * Override the new load_plugin function with the old package one
	 *
	 * @param string $plugin
	 * @param bool $callFromOld OPTIONAL Specify if the event was fired from the package function
	 */
	function compat_before_plugin(&$plugin, $callFromOld = false)
	{
		if (!$callFromOld) {
			package($plugin);
			$plugin = false;
		}
	}
	events_register('core_before_plugin', 'compat_before_plugin');
	
