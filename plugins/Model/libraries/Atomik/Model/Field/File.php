<?php

class Atomik_Model_Field_File extends Atomik_Model_Field_Abstract
{
	public function getFormHtml()
	{
		return '<input type="file" />';
	}
}