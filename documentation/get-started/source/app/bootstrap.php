<?php

Atomik::set(array(

	'layout' => '_layout',

	'styles' => array('main')
    
));

Atomik::set('plugins/Db', array(
	'dsn' 		=> 'mysql:host=localhost;dbname=atomik-blog',
	'username' 	=> 'atomik-blog',
	'password' 	=> 'atomik-blog'
));