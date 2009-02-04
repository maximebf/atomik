<?php

if (!count($_POST)) {
	return;
}

$rule = array(
	'title' => array('required' => true),
	'content' => array('required' => true)
);

if (($data = Atomik::filter($_POST, $rule)) === false) {
	foreach (A('filters/messages') as $message) {
		Atomik::flash($message, 'error');
	}
	return;
}

$data['publish_date'] = date('Y-m-d h:i:s');
Atomik_Db::insert('posts', $data);

Atomik::flash('Post successfully added!', 'success');
Atomik::redirect('index');