<?php

$form = new Atomik_Form('tata');
$form->addField(new Atomik_Form_Field_Input('name', array('required' => true)), 'Name');
$form->addField(new Atomik_Form_Field_Textarea('desc'), 'Description');

$subForm = new Atomik_Form('titi');
$subForm->addField(new Atomik_Form_Field_Input('toto', array('validate' => 'validate_email')), 'Toto');
$form->addField($subForm);

if ($form->hasData()) {
	var_dump($form->getData());
	var_dump($form->isValid());
	var_dump($form->getValidationMessages());
}

echo $form;