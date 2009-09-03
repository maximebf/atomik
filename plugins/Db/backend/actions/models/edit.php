<?php

if (!Atomik::has('request/name')) {
	Atomik::redirect('models');
}

$modelName = Atomik::get('request/name');
$returnUrl = Atomik::get('request/returnUrl', Atomik::url('models/list', array('name' => $modelName)));
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
$form->setAction(Atomik::url());
$form->setOption('cancel-url', $returnUrl);

if ($form->hasData()) {
	if ($form->isValid()) {
		$model = $form->getModel();
		$model->save();
		Backend_Activity::create('Models', $message, __(ucfirst($actionString) . ' by') . ' %s');
		Atomik::redirect($returnUrl, false);
	}
	Atomik::flash($form->getValidationMessages(), 'error');
}