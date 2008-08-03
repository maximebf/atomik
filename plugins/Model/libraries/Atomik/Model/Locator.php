<?php

/** Atomik_Model */
require_once 'Atomik/Model.php';

/**
 * Locates model
 */
class Atomik_Model_Locator
{
	/**
	 * Finds many models
	 *
	 * @param string|Atomik_Model_Builder $model
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return array
	 */
	public static function findAll($model, $where = null, $orderBy = '', $limit = '')
	{
		if (! $model instanceof Atomik_Model_Builder) {
			$model = new Atomik_Model_Builder($model);
		}
		
		return $model->getAdapter()->findAll($model, $where);
	}
	
	/**
	 * Finds one model
	 *
	 * @param string|Atomik_Model_Builder $model
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return Atomik_Model
	 */
	public static function find($model, $where, $orderBy = '', $limit = '')
	{
		if (! $model instanceof Atomik_Model_Builder) {
			$model = new Atomik_Model_Builder($model);
		}
		
		return $model->getAdapter()->find($model, $where, $orderBy = '', $limit = '');
	}
}