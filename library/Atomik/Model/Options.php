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
	public function hasOption($name, $prefix = '')
	{
		return array_key_exists($prefix . $name, $this->_options);
	}
	
	/**
	 * Returns an option
	 *
	 * @param string $name
	 * @param mixed $default OPTIONAL Default value if the key is not found
	 * @return mixed
	 */
	public function getOption($name, $default = null, $prefix = '')
	{
		if (!array_key_exists($prefix . $name, $this->_options)) {
			return $default;
		}
		return $this->_options[$prefix . $name];
	}
	
	/**
	 * Returns all options
	 * 
	 * @return array
	 */
	public function getOptions($prefix = null, $keepPrefixInResult = false)
	{
		if (empty($prefix)) {
			return $this->_options;
		}
		
		$options = array();
		foreach ($this->_options as $key => $value) {
			if (substr($key, 0, strlen($prefix)) == $prefix) {
				if (!$keepPrefixInResult) {
					$key = substr($key, strlen($prefix));
				}
				$options[$key] = $value;
			}
		}
		return $options;
	}
}