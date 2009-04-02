<?php

class Atomik_Model_Field_Bool extends Atomik_Model_Field_Abstract
{
	public function render($form)
	{
		$html = '<select name="%s"><option value="1">' 
			  . __('Yes') . '</option><option value="0">' 
			  . __('No') . '</option></select>';
		
		return sprintf(
			$html,
			$this->getName()
		);
	}
}