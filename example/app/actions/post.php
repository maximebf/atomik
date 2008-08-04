<?php

if (!isset($_GET['id'])) {
	Atomik::redirect('post');
}

$post = Atomik_Model::find('Post', array('id' => $_GET['id']));

if (count($_POST)) {
	$comment = new Comment($_POST['comment']);
	$post->add($comment);
	$comment->save();
}