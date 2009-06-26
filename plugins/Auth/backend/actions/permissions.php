<?php

$permissions = array();
foreach (AuthPlugin::getRestrictedUris() as $uri => $roles) {
	$permissions[] = array(
		'uri' => $uri,
		'roles' => implode(', ', $roles)
	);
}