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
}