<?php

/**
 * @table posts
 * @has many Comment as comments
 * @cascade-save
 */
class Post extends Atomik_Model
{
	public $title;
	
	/**
	 * @form-field Textarea
	 */
	public $body;
}