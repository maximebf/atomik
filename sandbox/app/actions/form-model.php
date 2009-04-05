<?php

class User extends Atomik_Model
{
	/**
	 * @form-label Name
	 */
	public $name;
	
	/**
	 * @form-field Textarea
	 * @form-label Description
	 */
	public $desc;
}

$form = new Atomik_Model_Form('User');

if ($form->hasModel()) {
	var_dump($form->getModel());
}

echo $form;