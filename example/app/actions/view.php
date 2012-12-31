<?php

if (!isset($this['request.id'])) {
    $this->flash('Missing id parameter', 'error');
    $this->redirect('index');
}

$post = $this['db']->selectOne('posts', array('id' => $this['request.id']));
