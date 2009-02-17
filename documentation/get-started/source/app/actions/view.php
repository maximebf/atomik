<?php

if (!isset($_GET['id'])) {
	Atomik::flash('Missing id parameter', 'error');
	Atomik::redirect('index');
}

$post = Atomik_Db::find('posts', array('id' => $_GET['id']));