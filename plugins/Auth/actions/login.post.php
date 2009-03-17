<?php

// filters the POST data
$data = Atomik::filter($_POST, array(
	'username' => array(
		'required' => true
	),
	'password' => array(
		'required' => true
	)
));

// checks if the data are valid
if ($data === false) {
	Atomik::flash(Atomik::get('app/filters/messages'), 'error');
	return;
}

// tries to authentify the user
if (Atomik_Auth::login($data['username'], $data['password'])) {
	// success, redirecting
	Atomik::redirect(Atomik::get('request/from', '/'));
}

// failed
Atomik::flash('The username or password is not valid', 'error');