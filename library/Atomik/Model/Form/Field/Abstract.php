<?php

abstract class Atomik_Model_Form_Field_Abstract extends Atomik_Model_Options implements Atomik_Model_Form_Field_Interface 
{
	public $name;
	
	public $label;
	
	public function __construct($name, $options = array())
	{
		$this->name = $name;
		$this->setOptions($options);
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