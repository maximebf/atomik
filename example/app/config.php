<?php

Atomik::set(array(

    'plugins' => array(
        'Db' => array(
            'dsn' => 'sqlite:example.db'
        ),
        'Session',
        'Flash'
    ),

    'app.layout' => '_layout'
    
));
