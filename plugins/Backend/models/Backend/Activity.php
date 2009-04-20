<?php

class Backend_Activity extends Atomik_Db_Object
{
	/**
	 * @var string
	 */
	protected $_table = 'backend_activities';
	
	/**
	 * Creates a new activity
	 * 
	 * @param	string	$label
	 * @param 	string	$message
	 * @param 	string	$userAction
	 * @param 	string	$date			SQL-formated date
	 * @return 	Backend_Activity
	 */
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
	 * Returns all activities
	 * 
	 * @param 	int|array	$limit
	 * @return 	Atomik_Db_Query_Result
	 */
	public static function findAll($limit = 20)
	{
		$activities = Atomik_Db::findAll('backend_activities', null, 'created DESC', $limit);
		$activities->setFetchMode(Atomik_Db_Query_Result::FETCH_OBJECT, null, 'Backend_Activity');
		return $activities;
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