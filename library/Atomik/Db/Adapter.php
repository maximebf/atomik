<?php

/** Atomik_Db_Adapter_Abstract */
require_once 'Atomik/Db/Adapter/Abstract.php';

class Atomik_Db_Adapter extends Atomik_Db_Adapter_Abstract
{
	/**
	 * Creates an instance of an adapter
	 * 
	 * @param 	string|objet 	$name		The last part of the adapter name if it starts with Atomik_Db_Adapter_ or a class name
	 * @return 	Atomik_Db_Adapter_Interface
	 */
	public static function factory($name, PDO $pdo)
	{
		$className = 'Atomik_Db_Adapter_' . ucfirst(strtolower($name));
		if (!class_exists($className)) {
			$className = $name;
			if (!class_exists($className)) {
			    require_once 'Atomik/Db/Adapter.php';
				$className = 'Atomik_Db_Adapter';
			}
		}
		
		return new $className($pdo);
	}
}