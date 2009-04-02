<?php

abstract class Atomik_Model_Options
{
	protected $_options = array();
	
	/**
	 * Sets all options
	 *
	 * @param array $options
	 */
	public function setOptions($options)
	{
		$this->_options = (array) $options;
	}
	
	/**
	 * Sets an option
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function setOption($name, $value)
	{
		$this->_options[$name] = $value;
	}
	
	/**
	 * Checks if an option exists
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasOption($name)
	{
		return array_key_exists($name, $this->_options);
	}
	
	/**
	 * Returns an option
	 *
	 * @param string $name
	 * @param mixed $default OPTIONAL Default value if the key is not found
	 * @return mixed
	 */
	public function getOption($name, $default = null)
	{
		if (!array_key_exists($name, $this->_options)) {
			return $default;
		}
		return $this->_options[$name];
	}
}