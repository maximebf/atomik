<?php
/**
 * Atomik Framework
 * Copyright (c) 2008-2009 Maxime Bouroumeau-Fuseau
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Atomik
 * @subpackage Db
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Db_Query */
require_once 'Atomik/Db/Query.php';

/** Atomik_Db_Adapter */
require_once 'Atomik/Db/Adapter.php';

/** Atomik_Db_Adapter_Interface */
require_once 'Atomik/Db/Adapter/Interface.php';

/**
 * Helpers function for handling databases
 *
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Instance
{
	/** @var PDO */
	public $pdo;
	
	/** @var array */
	public $connectionInfo;
	
	/** @var Atomik_Db_Adapter_Interface */
	protected $_adapter;
	
	/** @var string */
	protected $_tablePrefix = '';
	
	/** @var array */
	protected $_errorInfo;
	
	/** @var bool */
	protected $_inTransaction = false;
	
	/** @var string */
	protected static $_defaultTablePrefix = '';
	
	/**
	 * Sets the prefix that will be appended to all table names
	 * 
	 * @param string $prefix
	 */
	public static function setDefaultTablePrefix($prefix)
	{
		if (empty($prefix)) {
			$prefix = '';
		}
		self::$_defaultTablePrefix = $prefix;
	}
	
	/**
	 * Returns the table prefix
	 * 
	 * @return string
	 */
	public static function getDefaultTablePrefix()
	{
		return self::$_defaultTablePrefix;
	}
	
	/**
	 * Constructor
	 *
	 * @param PDO $pdo
	 */
	public function __construct($dsnOrPdo = null, $username = '', $password = '')
	{
		$this->_tablePrefix = self::getDefaultTablePrefix();
		
		if ($dsnOrPdo instanceof PDO) {
			$this->pdo = $pdo;
			return;
		}
		
		$this->setConnectionInfo($dsnOrPdo, $username, $password);
	}
	
	/**
	 * Sets the connection information
	 * 
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 */
	public function setConnectionInfo($dsn, $username, $password = '')
	{
		if ($this->pdo !== null) {
			require_once 'Atomik/Db/Instance/Exception.php';
			throw new Atomik_Db_Instance_Exception('Connection information cannot be set after the connection '
				. 'have been established, you must disconnect first');
		}
		
		$this->connectionInfo = array(
			'dsn' 		=> $dsn,
			'username' 	=> $username,
			'password' 	=> $password
		);
	}
	
	/**
	 * Returns connection information
	 * 
	 * @return array
	 */
	public function getConnectionInfo()
	{
		return $this->connectionInfo;
	}
	
	/**
	 * Connects to the database
	 * 
	 * If no params are provided, connection information
	 * will be used, {@see setConnectionInfo()}
	 * 
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 */
	public function connect($dsn = null, $username = '', $password = '')
	{
		if ($this->pdo !== null) {
			// already connected
			return;
		}
		
		if ($dsn !== null) {
			$this->setConnectionInfo($dsn, $username, $password);
		}
		$info = $this->getConnectionInfo();
		
		// creates the pdo instance
		try {
		    $this->pdo = new PDO($info['dsn'], $info['username'], $info['password']);
		} catch (Exception $e) {
			require_once 'Atomik/Db/Exception.php';
			throw new Atomik_Db_Exception('Database connection failed: ' . $e->getMessage());
		}
	}
	
	/**
	 * Closes the database connection
	 */
	public function disconnect()
	{
		$this->pdo = null;
		$this->_pdoDriver = null;
		$this->emptyCache();
	}
	
	/**
	 * Returns the managed PDO object
	 * 
	 * @return PDO
	 */
	public function getPdo()
	{
		$this->connect();
		return $this->pdo;
	}
	
	/**
	 * Returns the PDO driver being used
	 * 
	 * @return string
	 */
	public function getPdoDriverName()
	{
		$this->connect();
		return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
	}
	
	/**
	 * Returns the adapter associated to this instance
	 * 
	 * @return Atomik_Db_Adapter_Interface
	 */
	public function getAdapter()
	{
		if ($this->_adapter === null) {
			$this->connect();
			$this->_adapter = Atomik_Db_Adapter::factory($this->getPdoDriverName(), $this->pdo);
		}
		return $this->_adapter;
	}
	
	/**
	 * Returns a quoted value
	 * 
	 * @param string $text
	 * @return string
	 */
	public function quote($text)
	{
	    return $this->getAdapter()->quote($text);
	}
	
	/**
	 * Returns a quoted identifier
	 * 
	 * @param string $id
	 * @return string
	 */
	public function quoteIdentifier($id)
	{
	    return $this->getAdapter()->quoteIdentifier($id);
	}
	
	/**
	 * Sets the prefix that will be prepended to all table names
	 * 
	 * @param string $prefix
	 */
	public function setTablePrefix($prefix)
	{
		$this->_tablePrefix = $prefix;
	}
	
	/**
	 * Returns the table prefix
	 * 
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->_tablePrefix;
	}
	
	/**
	 * Returns error information
	 * 
	 * @param int $index
	 * @return array
	 */
	public function getErrorInfo($index = null)
	{
	    if ($index !== null) {
	        return $this->_errorInfo[$index];
	    }
		return $this->_errorInfo;
	}
	
	/**
	 * Creates a query associated to this instance
	 * 
	 * @return Atomik_Db_Query
	 */
	public function q()
	{
		$this->connect();
		return new Atomik_Db_Query($this);
	}
	
	/**
	 * Prepares and executes a statement
	 *
	 * @param string $query
	 * @param array $params
	 * @return PDOStatement
	 */
	public function query($query, $params = array())
	{
		$this->connect();
		
		if (($stmt = $this->pdo->prepare((string) $query)) === false) {
		    $this->_errorInfo = $this->pdo->errorInfo();
		    return false;
		}
		
		if (!$stmt->execute((array) $params)) {
		    $this->_errorInfo = $stmt->errorInfo();
			return false;
		}
		
		return $stmt;
	}
	
	/**
	 * Executes a statement
	 * 
	 * If $params is not empty, will use {@see query()}
	 *
	 * @see PDO::exec()
	 * @param string $sql
	 * @param array $params
	 * @return int Number of affected rows
	 */
	public function exec($sql, $params = null)
	{
		$this->connect();
		
		if (!empty($params)) {
		    return $this->query($sql, $params);
		}
		
		if (($nbRowAffected = $this->pdo->exec((string) $sql)) === false) {
		    $this->_errorInfo = $this->pdo->errorInfo();
		    return false;
		}
		return $nbRowAffected;
	}
	
	/**
	 * Prepares a statement
	 *
	 * @see PDO::prepare()
	 * @param string $query
	 * @param array $options
	 * @return PDOStatement
	 */
	public function prepare($query, $options = array())
	{
		$this->connect();
		if (($stmt = $this->pdo->prepare((string) $query, $options)) === false) {
		    $this->_errorInfo = $this->pdo->errorInfo();
		    return false;
		}
		return $stmt;
	}
	
	/**
	 * Starts a transaction
	 */
	public function beginTransaction()
	{
	    $this->connect();
	    $this->pdo->beginTransaction();
	    $this->_inTransaction = true;
	}
	
	/**
	 * Checks if a transaction is being used
	 * 
	 * @return bool
	 */
	public function isInTransaction()
	{
	    return $this->_inTransaction;
	}
	
	/**
	 * Commits a transaction
	 */
	public function commit()
	{
	    $this->connect();
	    $this->pdo->commit();
	    $this->_inTransaction = false;
	}
	
	/**
	 * Rollbacks a transaction
	 */
	public function rollback()
	{
	    $this->connect();
	    $this->pdo->rollBack();
	    $this->_inTransaction = false;
	}
	
	/**
	 * Finds the first row matching the arguments
	 *
	 * @see Atomik_Db_Instance::findAll()
	 * @param string $table
	 * @param array $where
	 * @param string $orderBy
	 * @param string $offset
	 * @param string|array $fields
	 * @return mixed
	 */
	public function find($table, $where = null, $orderBy = null, $offset = 0, $fields = null)
	{
		$limit = array($offset, 1);
		
		if (($result = self::findAll($table, $where, $orderBy, $limit, $fields)) === false) {
			return false;
		}
		
		$row = $result->fetch();
		$result->closeCursor();
		return $row;
	}
	
	/**
	 * Finds all rows matching the arguments
	 *
	 * @see Atomik_Db_Query
	 * @param string|array $table
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @param string|array $fields
	 * @return Atomik_Db_Query_Result
	 */
	public function findAll($table, $where = null, $orderBy = null, $limit = null, $fields = null)
	{
		$query = $this->_buildQuery($table, $where, $orderBy, $limit, $fields);
		return $query->execute();
	}
	
	/**
	 * Returns the value of the specified column of the first row to be found
	 * 
	 * @see Atomik_Db_Instance::find()
	 * @param string $table
	 * @param string $column
	 * @param array $where
	 * @param string $orderBy
	 * @param string $offset
	 * @return mixed
	 */
	public function findValue($table, $column, $where = null, $orderBy = null, $offset = 0)
	{
		if (($row = self::find($table, $where, $orderBy, $offset, array($column))) === false) {
			return false;
		}
		
		if (!array_key_exists($column, $row)) {
			return false;
		}
		return $row[$column];
	}
	
	/**
	 * Perform a SELECT COUNT(*) query
	 *
	 * @see Atomik_Db_Instance::buildWhere()
	 * @param string|Atomik_Db_Query $table
	 * @param array $where
	 * @return int
	 */
	public function count($table, $where = null)
	{
		if (!($table instanceof Atomik_Db_Query)) {
			$query = $this->_buildQuery($table, $where, null, null, 'COUNT(*)');
		} else {
			$query = clone $table;
			$query->count();
		}
		
		if (($result = $query->execute()) === false) {
			return false;
		}
		
		$count = $result->fetchColumn();
		$result->closeCursor();
		return $count;
	}
	
	/**
	 * Checks if some rows exist with the specified $where
	 * Kinf of an alias of {@see Atomik_Db_Instance::count()}
	 * 
	 * @param string|array $table
	 * @param array $where
	 * @return bool
	 */
	public function has($table, $where)
	{
		return $this->count($table, $where) > 0;
	}
	
	/**
	 * Inserts a row inside the database.
	 * $data must be an array where keys are column name
	 * and their associated value the value to insert in the
	 * database
	 *
	 * @param string $table
	 * @param array $data
	 * @return int Last inserted id
	 */
	public function insert($table, $data)
	{
	    $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)',
			$table,
			implode(', ', array_keys($data)),
			implode(', ', array_fill(0, count($data), '?'))
		);
		
		$params = array_values($data);
		$stmt = $this->prepare($sql);
	
		if (!$stmt->execute($params)) {
		    $this->_errorInfo = $stmt->errorInfo();
			return false;
		}
		return $this->pdo->lastInsertId();
	}
	
	/**
	 * Updates a row 
	 *
	 * @see Atomik_Db_Instance::buildWhere()
	 * @param string $table
	 * @param array $data
	 * @param array $where
	 * @return bool
	 */
	public function update($table, $data, $where)
	{
	    $sql = sprintf('UPDATE %s SET %s = ? WHERE %s = ?', 
	        $table, 
	        implode(' = ?, ', array_keys($data)), 
	        implode(' = ? AND ', array_keys($where))
	    );
	    
	    $params = array_merge(array_values($data), array_values($where));
		$stmt = $this->prepare($sql);
	
		if (!$stmt->execute($params)) {
		    $this->_errorInfo = $stmt->errorInfo();
			return false;
		}
		return true;
	}
	
	/**
	 * Deletes rows
	 *
	 * @see Atomik_Db_Instance::buildWhere()
	 * @param array|string $table
	 * @param array $where
	 * @return bool
	 */
	public function delete($table, $where)
	{
	    $sql = sprintf('DELETE FROM %s WHERE %s = ?', 
	        $table, 
	        implode(' = ? AND ', array_keys($where))
	    );
	    
	    $params = array_values($where);
		$stmt = $this->prepare($sql);
	
		if (!$stmt->execute($params)) {
		    $this->_errorInfo = $stmt->errorInfo();
			return false;
		}
		return true;
	}
	
	/**
	 * Inserts or updates values depending if they're already in the database.
	 * 
	 * Uses {@see Atomik_Db_Instance::has()} to check if data is already inserted.
	 * If $where is null, $data will be used as the where clause. $where can also
	 * be a string representing a key of the data array
	 * 
	 * @param string $table
	 * @param array $data
	 * @param array|string $where
	 * @return int|bool	Last insert id if it's an insert, true for success on update, false otherwise
	 */
	public function set($table, $data, $where = null)
	{
		if ($where === null) {
			$where = $data;
		} else if (is_string($where) && array_key_exists($where, $data)) {
			$where = array($where => $data[$where]);
		} else if (is_array($where)) {
			$tmpWhere = $where;
			$where = array();
			foreach ($tmpWhere as $key => $value) {
				if (is_int($key)) {
					if (array_key_exists($value, $data)) {
						$where[$value] = $data[$value];
					}
				} else {
					$where[$key] = $value;
				}
			}
		} else {
			return false;
		}
		
		if ($this->has($table, $where)) {
			return $this->update($table, $data, $where);
		}
		return $this->insert($table, $data);
	}
	
	/**
	 * Builds a Atomik_Db_Query object
	 * 
	 * @see Atomik_Db_Query
	 * @param string|array $table
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @param string|array $fields
	 * @return Atomik_Db_Query
	 */
	protected function _buildQuery($table, $where = null, $orderBy = null, $limit = null, $fields = null)
	{
		$query = $this->q()->select($fields)->from($table);
	
		if ($where !== null) {
			$query->where($where);
		}
		if ($orderBy !== null) {
			$query->orderBy($orderBy);
		}
		if ($limit !== null) {
			$query->limit($limit);
		}
		
		return $query;
	}
}

