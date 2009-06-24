<?php

if (!Atomik::has('request/name')) {
	Atomik::pluginRedirect('models/index');
}

$modelName = Atomik::get('request/name');
$builder = Atomik_Backend_Models::getModelBuilder($modelName);

$actionString = 'created';
$title = __('Create a new') . ' %s';
$message = __('A %s has been created', strtolower($modelName));

$model = $modelName;
if (Atomik::has('request/id')) {
	$model = Atomik_Model::find($builder, Atomik::get('request/id'));
	$actionString = 'modified';
	$title = __('Edit') . ' %s: ' . $model;
	$message = __('%s %s has been modified', $modelName, $model);
}

$form = new Atomik_Model_Form($model, array('form-', 'admin-form-'));

if ($form->hasData()) {
	if ($form->isValid()) {
		$model = $form->getModel();
		$model->save();
		Backend_Activity::create('Models', $message, __(ucfirst($actionString) . ' by') . ' %s');
		Atomik::pluginRedirect(Atomik::pluginUrl('models/list', array('name' => $modelName)), false);
	}
	Atomik::flash($form->getValidationMessages(), 'error');
}
