<?php

$form = new Atomik_Form();
$form->addField(new Atomik_Form_Field_Input('uri'), 'Uri: ');
$form->addField(new Atomik_Form_Field_Input('roles'), 'Roles (comma separated): ');

if ($form->hasData() && $form->isValid()) {
	$data = $form->getData();
	$roles = array_map('trim', explode(',', $data['roles']));
	$resources = Atomik::get('plugins/Auth/resources', array());
	$resources['/' . ltrim($data['uri'], '/')] = $roles;
	Atomik_Config::set('plugins/Auth/resources', $resources);
	Atomik::redirect('permissions');
}

if (($id = Atomik::get('request/id')) !== null) {
	foreach (AuthPlugin::getRestrictedUris() as $uri => $roles) {
		if ($id == $uri) {
			$form->setData(array('uri' => $uri, 'roles' => implode(', ', $roles)));
			break;
		}
	}
}