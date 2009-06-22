<?php

/**
 * @table comments
 * @has parent Post as post
 */
class Comment extends Atomik_Model
{
	/**
	 * @form-field Textarea
	 * @var string
	 */
	public $message;
	
	/**
	 * @form-ignore
	 * @admin-form-ignore false
	 * @admin-form-label Parent post
	 * @admin-show-in-list
	 * @var int
	 */
	public $post_id;
}