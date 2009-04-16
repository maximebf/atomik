<?php

/**
 * @table posts
 * @has many Comment as comment
 * @cascade-save
 */
class Post extends Atomik_Model
{
	public $title;
	
	/**
	 * @sql-type text
	 * @form-field Textarea
	 */
	public $body;
}