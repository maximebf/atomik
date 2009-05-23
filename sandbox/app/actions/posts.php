<?php

$form = new Atomik_Model_Form('Post');
if ($form->hasData()) {
	$model = $form->getModel();
	$model->created = new Atomik_Db_Query_Expr('NOW()');
	$model->save();
	$form->unsetModel();
}

$posts = Atomik_Model::findAll('Post');