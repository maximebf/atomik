<?php

/**
 * @Model(table="backend_activities")
 * @Timestampable
 */
class Backend_Activity extends Atomik_Model
{
    /**
     * @see Atomik_Model_Query::find()
     * @return Backend_Activity
     */
    public static function find($id)
    {
        return Atomik_Model_Query::find('Backend_Activity', $id);
    }
    
    /**
     * @see Atomik_Model_Query::findAll()
     * @return Atomik_Model_Collection
     */
    public static function findAll($where = array(), $orderBy = null, $limit = null)
    {
        return Atomik_Model_Query::findAll('Backend_Activity', $where, $orderBy, $limit);
    }
    
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
		$activity->setLabel($label);
		$activity->setMessage($message);
		$activity->setUserAction(sprintf($userAction, Atomik_Auth::getCurrentUsername()));
		$activity->save();
		return $activity;
	}
	
	/**
	 * @Field(type="string", length=50)
	 */
	protected $label;
	
	/**
	 * @Field(type="string", length=255)
	 */
	protected $message;
	
	/**
	 * @Field(type="string", length=100)
	 */
	protected $userAction;
}
