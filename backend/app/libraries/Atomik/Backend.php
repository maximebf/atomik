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
	    $confModules = Atomik::get('plugins/Backend/modules', array());
	    $availableModules = array();
	    
	    /* lists modules */
	    $dir = Atomik::path(Atomik::get('atomik/dirs/actions'));
	    foreach (new DirectoryIterator($dir) as $file) {
	        $filename = $file->getFilename();
	        if (substr($filename, 0, 1) == '.' || !$file->isDir()) {
	            continue;
	        }
	        $availableModules[] = $filename;
	    }
	    
	    $modules = array();
	    
	    foreach ($confModules as $filename => $module) {
	    	if (in_array($filename, $availableModules)) {
	    		$modules[$filename] = $module;
	    	}
	    }
	    
	    foreach ($availableModules as $filename) {
	    	if (!isset($confModules[$filename])) {
	    		$modules[$filename] = array(ucfirst($filename), 'left');
	    	}
	    }
		
	    return $modules;
	}
	
	/**
	 * Get the current module name, i.e. the current controller
	 *
	 * @return string
	 */
	public static function getModuleName()
	{
		return Atomik::get('controller_request/controller');
	}
}
