<?php

/**
 * @table comments
 * @has one Post as post
 */
class Comment extends Atomik_Model
{
	/**
	 * @field-type Atomik_Model_Field_Text
	 */
	public $message;
}