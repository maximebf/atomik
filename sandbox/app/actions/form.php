<?php

$form = new Atomik_Form();
$form->addField(new Atomik_Form_Field_Input('name', 'Name'));
$form->addField(new Atomik_Form_Field_Textarea('desc', 'Description'));

if ($form->hasData()) {
	var_dump($form->getData());
}

echo $form;