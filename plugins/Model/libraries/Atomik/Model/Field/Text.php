<?php

class Atomik_Model_Field_Text extends Atomik_Model_Field_Abstract
{
	public function render($form)
	{
		return sprintf(
			'<textarea name="%s">%s</textarea>',
			$this->getName(),
			$form->getModel()->{$this->getName()}
		);
	}
}