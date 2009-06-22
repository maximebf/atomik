<?php

/**
 * @table posts
 * @act-as Timestampable
 * @has many Comment as comments
 * @has parent User as author
 * @cascade-save
 */
class Post extends Atomik_Model
{
	/**
	 * @form-required
	 * @title-field
	 * @var string
	 * @length 100
	 */
	public $title;
	
	/**
	 * @sql-type text
	 * @form-id post-body
	 * @form-field RichTextarea
	 * @admin-hide-in-list
	 * @var string
	 */
	public $body;
}