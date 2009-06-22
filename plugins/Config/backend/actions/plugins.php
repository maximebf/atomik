<?php

$plugins = array();
$pluginDirs = (array) Atomik::get('atomik/dirs/plugins');

$cannotBeDisabledPlugins = array('Config', 'Backend');
foreach (Atomik::get('plugins') as $key => $value) {
	if ($key == 'Config') {
		break;
	}
	$cannotBeDisabledPlugins[] = $key;
}

foreach ($pluginDirs as $dir) {
	foreach (new DirectoryIterator($dir) as $file) {
		if ($file->isDot() || substr($file->getFilename(), 0, 1) == '.') {
			continue;
		}
		
		$plugin = $file->getFilename();
		if ($file->isFile()) {
			$plugin = substr($plugin, 0, strrpos($plugin, '.'));
		}
		
		$plugins[] = array(
			'id' => $file->getFilename(), 
			'name' => $plugin, 
			'is_loaded' => Atomik::isPluginLoaded($plugin),
			'can_be_disabled' => !in_array($plugin, $cannotBeDisabledPlugins)
		);
	}
}