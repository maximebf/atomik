<?php

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__ . '/../src',
    get_include_path()
)));

define('ATOMIK_AUTORUN', false);

spl_autoload_register(function($className) {
    if (substr($className, 0, 6) === 'Atomik') {
        $filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';
        require_once $filename;
    }
});
