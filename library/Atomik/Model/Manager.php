<?php

/** Atomik_Model_Modelset */
require_once 'Atomik/Model/Modelset.php';

class Atomik_Model_Manager
{
	/**
	 * @var Atomik_Db_Instance
	 */
	protected $_dbInstance;
	
	/**
	 * @var Atomik_Model_Behaviour_Broker
	 */
	protected $_globalBehaviourBroker;
	
	/**
	 * @var Atomik_Db_Instance
	 */
	protected static $_default;
	
	/**
	 * Sets the default manager
	 *
	 * @param Atomik_Model_Manager $db
	 */
	public static function setDefault(Atomik_Model_Manager $manager = null)
	{
		if ($manager === null) {
			$manager = new self(Atomik_Db::getInstance());
		}
		self::$_default = $manager;
	}
	
	/**
	 * Returns the default manager
	 *
	 * @return Atomik_Model_Manager
	 */
	public static function getDefault()
	{
		if (self::$_default === null) {
			self::setDefault();
		}
		return self::$_default;
	}
	
	/**
	 * Returns the builder from a db query
	 * 
	 * @param 	Atomik_Db_Query 	$query
	 * @return 	Atomik_Model_Builder
	 */
	public static function getBuilderFromQuery(Atomik_Db_Query $query)
	{
		$from = $query->getInfo('from');
		if (count($from) > 1) {
			require_once 'Atomik/Model/Manager/Exception.php';
			throw new Atomik_Model_Manager_Exception('Only one table can be used in the FROM clause with Atomik_Model_Manager');
		}
		
		$tableName = substr($from[0]['table'], strlen($query->getInstance()->getTablePrefix())); // remove the table prefix
		return Atomik_Model_Builder_Factory::getFromTableName($tableName);
	}
	
	/**
	 * Constructor
	 * 
	 * @param Atomik_Db_Instance $instance
	 */
	public function __construct(Atomik_Db_Instance $instance)
	{
		$this->_dbInstance = $instance;
		$this->_globalBehaviourBroker = new Atomik_Model_Behaviour_Broker();
	}
	
	/**
	 * Sets the associated db instance
	 * 
	 * @param Atomik_Db_Instance $instance
	 */
	public function setDbInstance(Atomik_Db_Instance $instance)
	{
		$this->_dbInstance = $instance;
	}
	
	/**
	 * Returns the db instance associated to this manager
	 * 
	 * @return Atomik_Db_Instance
	 */
	public function getDbInstance()
	{
		return $this->_dbInstance;
	}
	
	/**
	 * Returns the global behaviour broker
	 * 
	 * @return Atomik_Model_Behaviour_Broker
	 */
	public function getGlobalBehaviourBroker()
	{
		return $this->_globalBehaviourBroker;
	}
	
	/**
	 * Notify behaviour brokers
	 * 
	 * @param Atomik_Model_Builder $builder
	 * @param string $event
	 * @param array $args
	 */
	protected function _notify(Atomik_Model_Builder $builder, $event, $args)
	{
		array_unshift($args, $builder);
		$this->_globalBehaviourBroker->notify($event, $args);
		$builder->getBehaviourBroker()->notify($event, $args);
	}
	
	/**
	 * Query the adapter
	 * 
	 * @param	Atomik_Db_Query	$query
	 * @return 	Atomik_Model_Modelset
	 */
	public function query(Atomik_Db_Query $query)
	{
		if ($query->getInfo('statement') != 'SELECT') {
			require_once 'Atomik/Model/Manager/Exception.php';
			throw new Atomik_Model_Manager_Exception('Only SELECT queries can be used with Atomik_Model_Manager');
		}
		$builder = self::getBuilderFromQuery($query);
		
		$this->_notify($builder, 'BeforeQuery', array($query));
		
		if (($result = $this->_dbInstance->query($query)) === false) {
			return new Atomik_Model_Modelset($builder, array());
		}
		
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$modelSet = new Atomik_Model_Modelset($builder, $result);
		
		$this->_notify($builder, 'AfterQuery', array($modelSet));
		
		return $modelSet;
	}
	
	/**
	 * Wraps a query result into a model set
	 * 
	 * @param	Atomik_Db_Query_Result $result
	 * @return 	Atomik_Model_Modelset
	 */
	public function wrapResult(Atomik_Db_Query_Result $result)
	{
		$query = $result->getQuery();
		$builder = self::getBuilderFromQuery($query);
		return new Atomik_Model_Modelset($builder, $result);
	}
	
	/**
	 * Saves a model
	 *
	 * @param 	Atomik_Model $model
	 * @return 	bool
	 */
	public function save(Atomik_Model $model)
	{
		$builder = $model->getBuilder();
		$success = true;
		
		$this->_notify($builder, 'BeforeSave', array($model));
		
		$data = array();
		foreach ($builder->getFields() as $field) {
			$data[$field->name] = $field->filterOutput($model->{$field->name});
		}
		
		if ($model->isNew()) {
			// insert
			if (($id = $this->_dbInstance->insert($builder->tableName, $data)) === false) {
				$success = false;
			} else {
				$model->setPrimaryKey($id);
			}
		} else {
			// update
			$where = array($builder->getPrimaryKeyField()->name => $model->getPrimaryKey());
			$success = $this->_dbInstance->update($builder->tableName, $data, $where);
		}
		
		if (!$success) {
			$this->_notify($builder, 'FailSave', array($model));
			return false;
		}
		$this->_notify($builder, 'AfterSave', array($model));
		return true;
	}
	
	/**
	 * Deletes a model
	 *
	 * @param 	Atomik_Model $model
	 * @return 	bool
	 */
	public function delete(Atomik_Model $model)
	{
		if ($model->isNew()) {
			return false;
		}
		
		$builder = $model->getBuilder();
		$this->_notify($builder, 'BeforeDelete', array($model));
		
		$where = array($builder->getPrimaryKeyField()->name => $model->getPrimaryKey());
		if ($this->_dbInstance->delete($builder->tableName, $where) === false) {
			$this->_notify($builder, 'FailDelete', array($model));
			return false;
		}
		$this->_notify($builder, 'AfterDelete', array($model));
		return true;
	}
}