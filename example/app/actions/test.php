<?php


/**
 * @adapter Atomik_Model_Adapter_Local
 * @has many Album as albums
 */
class User extends Atomik_Model
{
	public $name;
}

/**
 * @adapter Atomik_Model_Adapter_Local
 * @has one User as user
 * @has many Image as images
 * @validate-on-save
 */
class Album extends Atomik_Model
{
	public $name;
	
	public $description;
}

/**
 * @adapter Atomik_Model_Adapter_Local
 * @has one Album as album
 */
class Image extends Atomik_Model
{
	/**
	 * @validate /.+/
	 * @required
	 */
	public $name;
	
	public $description;
	
	/**
	 * @field-type Atomik_Model_Field_File
	 */
	public $file;
}

$toto = new User();
$toto->name = 'toto';
$toto->save();

$album = new Album(array('name' => 'Holiday'));
$toto->albums[] = $album;
$album->save();

$image = new Image();
$image->name = 'The beach';
$album->images[] = $image;
$image->save();

//print_r(LocalModelAdapter::getInstance());

//print_r(Atomik_Model::findAll('Image'));

//$toto = Atomik_Model::find('User', array('name' => 'toto'));

echo $toto->name;
echo '<ul>';
foreach ($toto->albums as $album) {
	echo '<li>' . $album->name . '<ul>';
	foreach ($album->images as $image) {
		echo '<li>' . $image->name . '</li>';
	}
	echo '</ul></li>';
}
echo '</ul>';

$form = new Atomik_Model_Form($image);

if ($form->hasData()) {
	if ($form->isValid()) {
		$model = $form->getModel();
		echo $model->name . '<br>' . $model->description . '<br>' . $model->file;
	} else {
		print_r($form->getValidationMessages());
		exit;
	}
}

echo '<hr>';
echo $form;
