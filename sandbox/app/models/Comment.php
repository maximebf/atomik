<?php

/**
 * @table comments
 * @has parent Post as post
 */
class Comment extends Atomik_Model
{
	/**
	 * @sql-type text
	 * @form-field Textarea
	 */
	public $message;
	
	/**
	 * @form-ignore
	 * @admin-form-ignore false
	 * @admin-form-label Parent post
	 * @admin-show-in-list
	 */
	public $post_id;
}