<?php

Atomik::set(array(

    'plugins' => array(
        'Db' => array(
            'dsn' => 'sqlite:example.db'
        ),
        'Session',
        'Flash',
        'Errors' => array(
            'catch_errors' => true
        )
    ),

    'atomik.debug' => true,
    'app.layout' => '_layout'
    
));
