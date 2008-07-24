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
	    $modules = array();
	    
	    /* lists modules */
	    $dir = Atomik::path(Atomik::get('atomik/dirs/actions'));
	    foreach (new DirectoryIterator($dir) as $file) {
	        $filename = $file->getFilename();
	        if (substr($filename, 0, 1) == '.' || !$file->isDir()) {
	            continue;
	        }
	        if (!isset($confModules[$filename])) {
	            /* modules is not already defined in configuration */
	            $modules[$filename] = array(ucfirst($filename), 'left');
	        } else {
	            /* modules is defined in config */
	            $modules[$filename] = $confModules[$filename];
	        }
	    }
		
	    var_dump($confModules);
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
