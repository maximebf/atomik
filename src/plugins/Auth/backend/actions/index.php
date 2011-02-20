<?php

if (AuthPlugin::$config['model'] === null) {
	Atomik::setView('not_supported');
	return;
}

$descriptor = Atomik_Model_Descriptor::factory(AuthPlugin::$config['model']);
$users = new Atomik_Model_Query();
$users->from($descriptor)->filter(Atomik::get('request/filters', array()));

$columns = array();
foreach ($descriptor->getFields() as $field) {
	if ($field->name == 'password' || (($descriptor->isFieldThePrimaryKey($field) || $descriptor->isFieldPartOfReference($field) ||
		$field->hasOption('admin-hide-in-list')) && !$field->hasOption('admin-show-in-list'))) {
			continue;
	}
	$columns[$field->name] = $field->getLabel();
}