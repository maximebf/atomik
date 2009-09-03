<?php

if (($id = Atomik::get('request/id')) !== null) {
	foreach (AuthPlugin::getRestrictedUris() as $uri => $roles) {
		if ($id == $uri) {
			$resources = Atomik::get('plugins/Auth/resources', array());
			unset($resources['/' . $uri]);
			Atomik_Config::set('plugins/Auth/resources', $resources);
			Atomik::redirect('permissions');
		}
	}
}