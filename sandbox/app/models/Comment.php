<?php

/**
 * @table comments
 * @has parent Post as post
 */
class Comment extends Atomik_Model
{
	/**
	 * @form-field Textarea
	 */
	public $message;
}