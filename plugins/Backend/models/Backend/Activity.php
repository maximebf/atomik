<?php

/**
 * @adapter Db
 * @table backend_activities
 */
class Backend_Activity extends Atomik_Model
{
	public static function create($label, $message, $userAction = '', $date = null)
	{
		$activity = new self();
		$activity->label = $label;
		$activity->message = $message;
		$activity->userAction = $userAction;
		$activity->created = empty($date) ? @date('Y-m-d H:i:s') : $date;
		$activity->save();
		return $activity;
	}
	
	/**
	 * @sql-type varchar(50)
	 */
	public $label;
	
	/**
	 * @sql-type varchar(200)
	 */
	public $message;
	
	/**
	 * @sql-type varchar(200)
	 */
	public $userAction;
	
	/**
	 * @sql-type datetime
	 */
	public $created;
}