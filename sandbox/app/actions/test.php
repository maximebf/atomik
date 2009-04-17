<?php


/**
 * @adapter Local
 * @has many Album as albums
 */
class User extends Atomik_Model
{
	public $name;
}

/**
 * @adapter Local
 * @has parent User as user
 * @has many Image as images
 * @validate-on-save
 */
class Album extends Atomik_Model
{
	public $name;
	
	/**
	 * @form-field Textarea
	 */
	public $description;
}

/**
 * @adapter File
 * @has parent Album as album
 * @has one ImageMetadata as metadata
 * @dir app/uploads
 * @filename :album_id/:id.txt
 */
class Image extends Atomik_Model
{
	/**
	 * @filename
	 */
	public $filename;
	
	/**
	 * @file-content
	 */
	public $file;
}

/**
 * @adapter Local
 * @has parent Image as image
 */
class ImageMetadata extends Atomik_Model
{
	public $name;
}

$toto = new User();
$toto->name = 'toto';
$toto->save();

$album = new Album(array('name' => 'Holiday'));
$toto->albums[] = $album;
$album->save();

$meta = new ImageMetadata();
$meta->image_id = 0;
$meta->name = 'toto';
$meta->save();

unset($toto);
unset($album);
unset($image);


//print_r(LocalModelAdapter::getInstance());

//print_r(Atomik_Model::findAll('Employee'));

//$toto = Atomik_Model::find('User', array('name' => 'toto'));

echo '<ul>';
foreach (Atomik_Model::findAll('User') as $user) {
	echo '<li>' . $user->name . '<ul>';
	foreach ($user->albums as $album) {
		echo '<li>' . $album->name . '<ul>';
		foreach ($album->images as $image) {
			echo '<li>' . $image->metadata->name . ' (' . $image->filename . ')</li>';
		}
		echo '</ul></li>';
	}
	echo '</ul></li>';
}
echo '</ul>';

exit;

$form = new Atomik_Model_Form($image);

if ($form->hasModel()) {
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
