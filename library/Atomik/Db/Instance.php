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

/**
 * Helpers function for handling databases
 *
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Instance
{
	/**
	 * The pdo instance
	 *
	 * @var PDO
	 */
	public $pdo;
	
	/**
	 * Connection information for lazy loading
	 * 
	 * @var array
	 */
	public $connectionInfo;
	
	/**
	 * Constructor
	 *
	 * @param 	PDO 	$pdo
	 */
	public function __construct($dsnOrPdo = null, $username = null, $password = null)
	{
		if ($dsnOrPdo instanceof PDO) {
			$this->pdo = $pdo;
			return;
		}
		
		$this->connectionInfo = array(
			'dsn' 		=> $dsnOrPdo,
			'username' 	=> $username,
			'password' 	=> $password
		);
	}
	
	/**
	 * Connects to the database using the config values
	 */
	public function connect($dsn = null, $username = null, $password = null)
	{
		if ($this->pdo !== null) {
			// already connected
			return;
		}
		
		if ($dsn === null && $this->connectionInfo !== null) {
			$dsn = $this->connectionInfo['dsn'];
			$username = $this->connectionInfo['username'];
			$password = $this->connectionInfo['password'];
		}
		
		// creates the pdo instance
		try {
		    $this->pdo = new PDO($dsn, $username, $password);
			$this->connectionInfo = array(
				'dsn' 		=> $dsn,
				'username' 	=> $username,
				'password' 	=> $password
			);
		} catch (Exception $e) {
			require_once 'Atomik/Db/Exception.php';
			throw new Atomik_Db_Exception('Database connection failed');
		}
	}
	
	/**
	 * Closes the database connection
	 */
	public function disconnect()
	{
		$this->pdo = null;
	}
	
	/**
	 * Prepares and executes a statement
	 *
	 * @param 	string|Atomik_Db_Query	$query
	 * @param 	array 					$params
	 * @return 	PDOStatement
	 */
	public function query($query, $params = array())
	{
		if ($query instanceof Atomik_Db_Query) {
			$params = $query->getParams();
		}
		
		$this->connect();
		$stmt = $this->pdo->prepare((string) $query);
		$stmt->execute($params);
		
		return $stmt;
	}
	
	/**
	 * Executes a query without results
	 *
	 * @see PDO::exec()
	 * @param 	string 		$query
	 * @return 	int|bool
	 */
	public function exec($query)
	{
		$this->connect();
		return $this->pdo->exec((string) $query);
	}
	
	/**
	 * Prepares a statement
	 *
	 * @see PDO::prepare()
	 * @param 	string 			$query
	 * @param 	array 			$options
	 * @return 	PDOStatement
	 */
	public function prepare($query, $options = array())
	{
		$this->connect();
		return $this->pdo->prepare((string) $query, $options);
	}
	
	/**
	 * Finds the first row matching the arguments
	 *
	 * @see Db::buildWhere()
	 * @param 	string 			$table
	 * @param 	array 			$where
	 * @param 	string 			$orderBy
	 * @param 	string 			$limit
	 * @param 	string|array 	$fields
	 * @return 	array
	 */
	public function find($table, $where = null, $orderBy = null, $limit = null, $fields = null)
	{
		$stmt = self::findAll($table, $where, $orderBy, $limit, $fields);
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row;
	}
	
	/**
	 * Finds all rows matching the arguments
	 *
	 * @param 	string|array 	$table
	 * @param 	array 			$where
	 * @param 	string 			$orderBy
	 * @param 	string 			$limit
	 * @param 	string|array 	$fields
	 * @return 	array
	 */
	public function findAll($table, $where = null, $orderBy = null, $limit = null, $fields = null)
	{
		$this->connect();
		$query = $this->_buildQuery($table, $where, $orderBy, $limit, $fields);
		return $query->execute($this->pdo);
	}
	
	/**
	 * Perform a SELECT COUNT(*) query
	 *
	 * @see Db::buildWhere()
	 * @param 	string|array 	$table
	 * @param 	array 			$where
	 * @param 	string 			$limit
	 * @return 	int
	 */
	public function count($table, $where = null, $limit = '')
	{
		$this->connect();
		
		$query = $this->_buildQuery($table, $where, null, $limit, 'COUNT(*)');
		$stmt = $query->execute($this->pdo);
		$count = $stmt->fetchColumn();
		$stmt->closeCursor();
		
		return $count;
	}
	
	/**
	 * Inserts a row inside the database.
	 * $data must be an array where keys are column name
	 * and their associated value the value to insert in the
	 * database
	 *
	 * @param 	string 		$table
	 * @param 	array 		$data
	 * @return 	bool|int 			Last insert id or false
	 */
	public function insert($table, $data)
	{
		$this->connect();
		
		$query = new Atomik_Db_Query();
		$stmt = $query->insertInto($table)->values($data)->execute($this->pdo);
	
		if ($stmt === false) {
			return false;
		}
		return $this->pdo->lastInsertId();
	}
	
	/**
	 * Updates a row 
	 *
	 * @see Db::buildWhere()
	 * @param 	string 	$table
	 * @param 	array 	$data
	 * @param 	array 	$where
	 * @return 	bool
	 */
	public function update($table, $data, $where)
	{
		$this->connect();
		
		$query = new Atomik_Db_Query();
		return $query->update($table)->set($data)->where($where)->execute($this->pdo);
	}
	
	/**
	 * Deletes rows
	 *
	 * @see Db::buildWhere()
	 * @param 	array|string 	$table
	 * @param 	array 			$where
	 * @return 	bool
	 */
	public function delete($table, $where = array())
	{
		$this->connect();
		$query = new Atomik_Db_Query();
		return $query->delete()->from($table)->where($where)->execute($this->pdo);
	}
	
	/**
	 * Builds a Atomik_Db_Query object
	 * 
	 * @see Atomik_Db_Query
	 * @param 	string|array 	$table
	 * @param 	array 			$where
	 * @param 	string 			$orderBy
	 * @param 	string 			$limit
	 * @param 	string|array 	$fields
	 * @return Atomik_Db_Query
	 */
	protected function _buildQuery($table, $where = null, $orderBy = null, $limit = null, $fields = null)
	{
		$query = new Atomik_Db_Query();
		$query->from($table);
	
		if ($where !== null) {
			$query->where($where);
		}
		if ($orderBy !== null) {
			$query->orderBy($orderBy);
		}
		if ($limit !== null) {
			$query->limit($limit);
		}
		if ($fields !== null) {
			$query->select($fields);
		}
		
		return $query;
	}
}

