<?php

$form = new Atomik_Model_Form('Post');
if ($form->hasData()) {
	$form->getModel()->save();
	$form->unsetModel();
}

$posts = Atomik_Model::findAll('Post');