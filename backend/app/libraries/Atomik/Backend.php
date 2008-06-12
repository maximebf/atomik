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
		return Atomik::get('backend/modules');
	}
	
	/**
	 * Get the current module name, i.e. the current controller
	 *
	 * @return string
	 */
	public static function getModuleName()
	{
		return Atomik::get('controller/request/controller');
	}
}
