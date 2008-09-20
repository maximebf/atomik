<?php

/**
 * @table posts
 * @has-many Comment as comments
 */
class Post extends Atomik_Model
{
	public $id;
	
	public $title;
	
	public $body;
}