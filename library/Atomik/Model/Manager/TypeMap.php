<?php

class Atomik_Model_Manager_TypeMap
{
	protected $_manager;
	
	public function __construct(Atomik_Model_Manager $manager)
	{
		$this->_manager = $manager;
	}
	
	public function map($type, $length = null)
	{
		$method = 'map' . ucfirst(strtolower($type));
		if (method_exists($this, $method)) {
			return $this->{$method}($length);
		}
		
		return array($type, $length);
	}
	
	public function mapString($length)
	{
		$type = 'text';
		if ($length !== null && $length <= 255) {
			$type = 'varchar';
		}
		return array($type, $length);
	}
}