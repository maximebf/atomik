<?php
/**
 * @adapter Local
 * @has-many Album as albums
 */
class User extends Atomik_Model
{
	public $id;
	
	public $name;
}

/**
 * @adapter Local
 * @has-one User as user
 * @has-many Image as images
 */
class Album extends Atomik_Model
{
	public $id;
	
	public $user_id;
	
	public $name;
	
	public $description;
}

/**
 * @adapter File
 * @adapter Local
 * @primary-key id
 * 
 * @has-one Album as album
 * @content content
 * @path :album_id/:id.txt
 * @dir app/uploads/
 */
class Image extends Atomik_Model
{
	public $id;
	
	/**
	 * @adapter File
	 */
	public $album_id;
	
	/**
	 * @adapter File
	 */
	public $content;
	
	/**
	 * @adapter Local
	 */
	public $name;
	
	/**
	 * @adapter Local
	 */
	public $description;
}

$toto = new User();
$toto->name = 'toto';
$toto->save();

$album = new Album(array('name' => 'Holiday'));
$toto->add($album);
$album->save();

$image = Atomik_Model::find('Image', array('id' => 1, 'album_id' => 1));
$image->name = 'toto';
$image->save();
$album->add($image);

//print_r(LocalModelAdapter::getInstance());

//print_r(Atomik_Model::findAll('Image'));

//$toto = Atomik_Model::find('User', array('name' => 'toto'));

echo $toto->name;
echo '<ul>';
foreach ($toto->albums as $album) {
	echo '<li>' . $album->name . '<ul>';
	foreach ($album->images as $image) {
		echo '<li>' . $image->name . ':' . $image->content . '</li>';
	}
	echo '</ul></li>';
}
echo '</ul>';
