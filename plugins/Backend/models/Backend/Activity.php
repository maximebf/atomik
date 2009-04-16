<?php

/**
 * @table activities
 */
class Backend_Activity extends Atomik_Model
{
	/**
	 * @sql-type varchar(50)
	 */
	public $label;
	
	/**
	 * @sql-type varchar(200)
	 */
	public $message;
	
	/**
	 * @sql-type datetime
	 */
	public $created;
}