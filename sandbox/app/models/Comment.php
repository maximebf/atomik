<?php

/**
 * @table comments
 * @has one Post as post
 */
class Comment extends Atomik_Model
{
	/**
	 * @form-field Text
	 */
	public $message;
}