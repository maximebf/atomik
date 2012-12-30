<?php

$fields = array(
    'title' => array('required' => true),
    'content' => array('required' => true)
);

if (($data = $this->filter($_POST, $fields)) === false) {
    $this->flash($this['app.filters.messages'], 'error');
    return;
}

$this['db']->insert('posts', $data);

$this->flash('Post successfully added!', 'success');
$this->redirect('index');
