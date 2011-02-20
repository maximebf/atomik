<?php

/**
 * @class form form-content
 */
class SettingsForm extends Atomik_Form_Class
{
	/**
	 * @label Default action
	 * @class form-input
	 */
	public $default_action;
	
	/**
	 * @label Layout
	 * @class form-input
	 */
	public $layout;
}

$form = new SettingsForm();

if (!count($_POST)) {
	$form->setData(array(
		'default_action' => Atomik::get('userapp/default_action'),
		'layout' => Atomik::get('userapp/layout')
	));
}