<?php

$form = new Atomik_Model_Form('Post');
if ($form->hasData()) {
	$form->getModel()->save();
	$form->unsetModel();
}

$posts = Atomik_Model::findAll('Post');
var_dump(Atomik_Model_Builder_Factory::get('Comment'));