<?php

/**
 * Helper functions for the backend
 */
class Atomik_Backend
{
	/**
	 * Gets the list of modules. A module is basically an
	 * action file, so we list files in the actions directory
	 *
	 * @return array
	 */
	public static function getModules()
	{
		return config_get('backend_modules');
	}
	
	/**
	 * Get the current module name, i.e. the current controller
	 *
	 * @return string
	 */
	public static function getModuleName()
	{
		$request = config_get('controller_request');
		return $request['controller'];
	}
}
