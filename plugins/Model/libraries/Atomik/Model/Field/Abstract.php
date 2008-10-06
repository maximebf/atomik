<?php

class Atomik_Model_Field_Abstract
{
	protected $_name;
	
	protected $_label;
	
	protected $_options = array();
	
	protected $_validationMessages = array();
	
	public function __construct($name, $options = array())
	{
		$this->setName($name);
		$this->setOptions($options);
	}

	public function setName($name)
	{
		$this->_name = $name;
	}
	
	public function getName()
	{
		return $this->_name;
	}
	
	public function setLabel($label)
	{
		$this->_label = $label;
	}
	
	public function getLabel()
	{
		if ($this->_label === null) {
			return $this->_name;
		}
		return $this->_label;
	}
	
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
	
	/**
	 * Checks if the specified value is valid
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public function isValid($value)
	{
		$isValid = true;
		$this->_validationMessages = array();
			
		if ($this->hasOption('validate')) {
			if(!preg_match($this->getOption('validate'), $value)) {
				$this->_validationMessages[] = $this->_name . ' failed to validate because it '
											 . 'didn\'t match the regexp: ' . $this->getOption('validate');
				return false;
			}
			
			return true;
		}
		
		if ($this->hasOption('validate-with')) {
			if (strpos($this->getOption('validate-with'), '::') !== false) {
				$callback = explode('::', $this->getOption('validate-with'));
			} else {
				$callback = $this->getOption('validate-with');
			}
			
			if (!call_user_func($callback, $value)) {
				$this->_validationMessages[] = $this->_name . ' failed to validate because '
											 . $this->getOption('validate-with') . '() returned false';
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Returns the messages generated during the validation
	 *
	 * @return array
	 */
	public function getValidationMessages()
	{
		return $this->_validationMessages;
	}
	
	public function getValue($formValue)
	{
		return $formValue;
	}
	
	public function getDefaultValue()
	{
		if ($this->hasOption('default')) {
			return $this->getOption('default');
		}
		return null;
	}
	
	public function render(Atomik_Model_Form $form)
	{
		return sprintf(
			'<input type="text" name="%s" value="%s" />',
			$this->getName(),
			$form->getModel()->{$this->getName()}
		);
	}
}