<?php

/** Atomik_Model */
require_once 'Atomik/Model.php';

/** Atomik_Model_Adapter_Interface */
require_once 'Atomik/Model/Adapter/Interface.php';

/**
 * 
 *
 */
class Atomik_Model_Adapter_Table implements Atomik_Model_Adapter_Interface
{
	/**
	 * Singleton instance
	 *
	 * @var Atomik_Model_Adapter_Table
	 */
	protected static $_instance;
	
	/**
	 * Gets the singleton
	 *
	 * @return Atomik_Model_Adapter_Table
	 */
	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Finds many models
	 *
	 * @param Atomik_Model_Builder $builder
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return array
	 */
	public function findAll(Atomik_Model_Builder $builder, $where = null, $orderBy = '', $limit = '')
	{
		$rows = Db::findAll($this->getTableName($builder), $where, $orderBy, $limit);
		$models = array();
		
		foreach ($rows as $row) {
			$models[] = $builder->createInstance($row, false);
		}
		
		return $models;
	}
	
	/**
	 * Finds one model
	 *
	 * @param Atomik_Model_Builder $builder
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return Atomik_Model
	 */
	public function find(Atomik_Model_Builder $builder, $where, $orderBy = '', $limit = '')
	{
		$row = Db::find($this->getTableName($builder), $where, $orderBy, $limit);
		return $builder->createInstance($row, false);
	}
	
	/**
	 * Saves a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function save(Atomik_Model $model)
	{
		$data = $model->toArray();
		$builder = $model->getBuilder();
		$tableName = $this->getTableName($builder);
		$primaryKey = $this->getPrimaryKey($builder);
		
		// insert
		if ($model->isNew()) {
			if (($id = Db::insert($tableName, $data)) === false) {
				return false;
			}
			$model->{$primaryKey} = $id;
			return true;
		}
		
		// update
		$where = array($primaryKey => $model->{$primaryKey});
		return Db::update($tableName, $data, $where);
	}
	
	/**
	 * Deletes a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function delete(Atomik_Model $model)
	{
		if ($model->isNew()) {
			return;
		}
		
		$builder = $model->getBuilder();
		$tableName = $this->getTableName($builder);
		$primaryKey = $this->getPrimaryKey($builder);
		
		$where = array($primaryKey => $model->{$primaryKey});
		return Db::delete($tableName, $where);
	}
	
	/**
	 * Gets the table name associated to a model
	 *
	 * @param Atomik_Model_Builder $builder
	 * @return string
	 */
	protected function getTableName($builder)
	{
		$table = $builder->getMetadata('table');
		if ($table === null) {
			throw new Exception('Table not set on model ' . $builder->getClass());
		}
		return $table;
	}
	
	/**
	 * Gets the primary key of the associated table
	 *
	 * @param Atomik_Model_Builder $builder
	 * @return string
	 */
	protected function getPrimaryKey($builder)
	{
		return $builder->getMetadata('primary-key', 'id');
	}
}