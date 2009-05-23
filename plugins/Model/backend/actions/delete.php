<?php

if (!Atomik::has('request/name') || !Atomik::has('request/id')) {
	Atomik::pluginRedirect('index');
}

$modelName = Atomik::get('request/name');
$model = Atomik_Model::find($modelName, Atomik::get('request/id'));
$title = (string) $model;

if (!$model->delete()) {
	Atomik::flash(__('An error occured while deleting %s %s', strtolower($modelName), $title), 'error');
} else {
	Atomik::flash(__('%s %s successfully deleted', $modelName, $title), 'success');
	Backend_Activity::create('Models', __('%s %s has been deleted', $modelName, $title), __('Deleted by') . ' %s');
}

Atomik::pluginRedirect(Atomik::pluginUrl('list', array('name' => $modelName)), false);