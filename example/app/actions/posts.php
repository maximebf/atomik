<?php

if (count($_POST)) {
	$post = new Post($_POST['post']);
	$post->save();
}

$posts = Atomik_Model::findAll('Post');