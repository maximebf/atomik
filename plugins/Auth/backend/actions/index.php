<?php

$builder = Atomik_Model_Builder_Factory::get(AuthPlugin::$config['model']);
$users = Atomik_Model_Query::create()->from($builder);

$columns = array();
foreach ($builder->getFields() as $field) {
	if ($field->name == 'password' || ($builder->isFieldThePrimaryKey($field) || $builder->isFieldPartOfReference($field) ||
		$field->hasOption('admin-hide-in-list')) && !$field->hasOption('admin-show-in-list')) {
			continue;
	}
	$columns[] = $field->name;
}