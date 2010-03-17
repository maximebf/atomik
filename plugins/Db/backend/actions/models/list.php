<?php

if (!Atomik::has('request/name')) {
	Atomik::redirect('index');
}

$modelName = Atomik::get('request/name');
$descriptor = Atomik_Backend_Models::getModelDescriptor($modelName);

$models = new Atomik_Model_Query();
$models->from($descriptor)->filter(Atomik::get('request/filters', array()));

if (isset($_POST['search'])) {
	$models->where($_POST['searchBy'] . ' LIKE ?', '%' . $_POST['search'] . '%');
}

$columns = array();
foreach ($descriptor->getFields() as $field) {
	if (($descriptor->isFieldThePrimaryKey($field) || $descriptor->isFieldPartOfReference($field) ||
		$field->hasOption('admin-hide-in-list')) && !$field->hasOption('admin-show-in-list')) {
			continue;
	}
	$columns[$field->name] = $field->getLabel();
}

$editUrl = Atomik::get('request/editUrl', Atomik::url('models/edit', array('name' => $modelName)));
$deleteUrl = Atomik::get('request/deleteUrl', Atomik::url('models/delete', array('name' => $modelName)));