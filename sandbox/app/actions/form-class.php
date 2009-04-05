<?php

class MyForm extends Atomik_Form_Class
{
	/**
	 * @label Name
	 */
	public $name;
	
	/**
	 * @field Textarea
	 * @label Description
	 */
	public $desc;
}

$form = new MyForm();

if ($form->hasData()) {
	var_dump($form);
}

echo $form;