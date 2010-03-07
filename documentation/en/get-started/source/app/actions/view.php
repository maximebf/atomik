<?php

if (!Atomik::has('request/id')) {
	Atomik::flash('Missing id parameter', 'error');
	Atomik::redirect('index');
}

$post = Atomik_Db::find('posts', array('id' => A('request/id')));