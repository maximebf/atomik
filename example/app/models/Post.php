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
	 * @field-type Atomik_Model_Field_Text
	 */
	public $body;
}