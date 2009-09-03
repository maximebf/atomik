<?php

if (AuthPlugin::$config['model'] === null) {
	Atomik::setView('not_supported');
	return;
}

$builder = Atomik_Model_Builder_Factory::get(AuthPlugin::$config['model']);
$users = new Atomik_Model_Query();
$users->from($builder)->filter(Atomik::get('request/filters', array()));

$columns = array();
foreach ($builder->getFields() as $field) {
	if ($field->name == 'password' || (($builder->isFieldThePrimaryKey($field) || $builder->isFieldPartOfReference($field) ||
		$field->hasOption('admin-hide-in-list')) && !$field->hasOption('admin-show-in-list'))) {
			continue;
	}
	$columns[$field->name] = $field->getLabel();
}