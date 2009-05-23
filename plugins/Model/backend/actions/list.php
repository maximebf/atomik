<?php

if (!Atomik::has('request/name')) {
	Atomik::pluginRedirect('index');
}

$modelName = Atomik::get('request/name');
$builder = Atomik_Backend_Models::getModelBuilder($modelName);
$models = Atomik_Model::findAll($builder);

$columns = array();
foreach ($builder->getFields() as $field) {
	if (($builder->isFieldThePrimaryKey($field) || $builder->isFieldPartOfReference($field) ||
		$field->hasOption('admin-hide-in-list')) && !$field->hasOption('admin-show-in-list')) {
			continue;
	}
	$columns[] = $field->name;
}