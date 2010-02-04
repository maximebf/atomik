<?php

define('ATOMIK_AUTORUN', false);
require dirname(__FILE__) . '/../Atomik.php';
Atomik::set('atomik/scriptname', __FILE__);
Atomik::run();
