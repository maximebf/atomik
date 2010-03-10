<?php

class Atomik_Backend_Model
{
	public static function factory($modelId, $id)
	{
		if (($row = Db::find('models', array('id' => $modelId))) === false) {
			return false;
		}
		
		$className = 'Atomik_Backend_Model_Adapter_' . ucfirst($row['adapter']);
		$filename = str_replace('_', '/', $className) . '.php';
		require_once $filename;
		
		if (!class_implements($className, 'Atomik_Backend_Model_Adapter_Interface')) {
			throw new Atomik_Backend_Exception('Model adapter must implements the adapter interface');
		}
		
		return call_user_func(array($className, 'getValues'), $id, $modelId);
	}
}