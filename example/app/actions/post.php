<?php

if (!isset($_GET['id'])) {
	Atomik::redirect('posts');
}

$post = Atomik_Model::find('Post', array('id' => $_GET['id']));

$form = new Atomik_Model_Form('Comment');
if ($form->hasData()) {
	$comment = $form->getModel();
	$post->comments[] = $comment;
	$comment->save();
	$form->unsetModel();
}