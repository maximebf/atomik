<?php

Atomik::set(array(
    'plugins.Db' => array(
        'dsn'         => 'mysql:host=localhost;dbname=blog',
        'username'     => 'root',
        'password'     => ''
    ),

    'app.layout' => '_layout',

    'styles' => array('main.css')
));
