<?php

/**
 * @table comments
 * @has-one Post as post
 */
class Comment extends Atomik_Model
{
	public $id;
	
	public $post_id;
	
	public $message;
}