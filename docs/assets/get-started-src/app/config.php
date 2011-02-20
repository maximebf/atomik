<?php

Atomik::set(array(

	'app/layout' => '_layout',

	'styles' => array('main')
    
));

Atomik::set('plugins/Db', array(
	'dsn' 		=> 'mysql:host=localhost;dbname=blog',
	'username' 	=> 'root',
	'password' 	=> ''
));