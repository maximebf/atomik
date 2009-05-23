<?php

/**
 * @table posts
 * @has many Comment as comments
 * @has parent User as author
 * @cascade-save
 */
class Post extends Atomik_Model
{
	/**
	 * @form-required
	 * @title-field
	 */
	public $title;
	
	/**
	 * @sql-type text
	 * @form-id post-body
	 * @form-field RichTextarea
	 * @admin-hide-in-list
	 */
	public $body;
	
	/**
	 * @sql-type datetime
	 * @form-field Datetime
	 * @form-ignore
	 * @admin-form-ignore false
	 */
	public $created;
}