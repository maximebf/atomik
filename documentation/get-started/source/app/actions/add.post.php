<?php

$rule = array(
	'title' => array('required' => true),
	'content' => array('required' => true)
);

if (($data = Atomik::filter($_POST, $rule)) === false) {
	Atomik::flash(A('app/filters/messages'), 'error');
	return;
}

$data['publish_date'] = date('Y-m-d h:i:s');
Atomik_Db::insert('posts', $data);

Atomik::flash('Post successfully added!', 'success');
Atomik::redirect('index');