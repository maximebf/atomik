<?php

if (!Atomik::has('request/name')) {
	Atomik::redirect('plugins');
}

$name = Atomik::get('request/name');
$enable = Atomik::get('request/enable', 'true');

if ($enable == 'false' || !$enable) {
	Atomik_Config::set('plugins/' . $name, false);
	Atomik::flash(__('%s has been disabled', $name), 'success');
} else {
	Atomik_Config::set('plugins/' . $name, array());
	Atomik::flash(__('%s has been enabled', $name), 'success');
}

Atomik::redirect('plugins');