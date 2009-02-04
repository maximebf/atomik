<?php
/**
 * Atomik Framework
 * Copyright (c) 2008 Maxime Bouroumeau-Fuseau
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
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
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
	 * Constructor
	 *
	 * @param PDO $pdo OPTIONAL A PDO instance
	 */
	public function __construct($pdo = null)
	{
		$this->pdo = $pdo;
	}
	
	/**
	 * Connects to the database using the config values
	 */
	public function connect($dsn = null, $username = null, $password = null)
	{
		/* creates the pdo instance */
		try {
		    $this->pdo = new PDO($dsn, $username, $password);
		} catch (Exception $e) {
			require_once 'Atomik/Db/Exception.php';
			throw new Atomik_Db_Exception('Database connection failed');
		}
	}
	
	/**
	 * Prepares and executes a statement
	 *
	 * @param string $query
	 * @param array $params OPTIONAL
	 * @return PDOStatement
	 */
	public function query($query, $params = array())
	{
		$stmt = $this->pdo->prepare($query);
		$stmt->execute($params);
		return $stmt;
	}
	
	/**
	 * Executes a query without results
	 *
	 * @see PDO::exec()
	 * @param string $query
	 * @return int|bool
	 */
	public function exec($query)
	{
		return $this->pdo->exec($query);
	}
	
	/**
	 * Prepares a statement
	 *
	 * @see PDO::prepare()
	 * @param string $query
	 * @param array $options OPTIONAL
	 * @return PDOStatement
	 */
	public function prepare($query, $options = array())
	{
		return $this->pdo->prepare($query, $options);
	}
	
	/**
	 * Finds the first row matching the arguments
	 *
	 * @see Db::buildWhere()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @param string|array $fields OPTIONAL
	 * @return array
	 */
	public function find($tables, $where = null, $orderBy = '', $limit = '', $fields = '*')
	{
		$stmt = $this->_executeSelect($tables, $where, $orderBy, $limit, $fields);
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row;
	}
	
	/**
	 * Finds all rows matching the arguments
	 *
	 * @see Db::buildWhere()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @param string|array $fields OPTIONAL
	 * @return array
	 */
	public function findAll($tables, $where = null, $orderBy = '', $limit = '', $fields = '*')
	{
		return $this->_executeSelect($tables, $where, $orderBy, $limit, $fields);
	}
	
	/**
	 * Perform a SELECT COUNT(*) query
	 *
	 * @see Db::buildWhere()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @return int
	 */
	public function count($tables, $where = null, $orderBy = '', $limit = '')
	{
		$stmt = $this->_executeSelect($tables, $where, $orderBy, $limit, 'COUNT(*)');
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
	 * @param string $table
	 * @param array $data
	 * @return bool|int Last insert id or false
	 */
	public function insert($table, $data)
	{
		$fields = array_keys($data);
		$values = array_values($data);

		/* builds the sql string */
		$sql = 'INSERT INTO ' . $table . '(' . implode(', ', $fields)
			   . ') VALUES(' . implode(', ', array_fill(0, count($values), '?')) . ')';
	
		/* creates and executes the statement */
		$stmt = $this->pdo->prepare($sql);
		if ($stmt->execute($values)) {
		    return $this->pdo->lastInsertId();
		}
		return false;
	}
	
	/**
	 * Updates a row 
	 *
	 * @see Db::buildWhere()
	 * @param string $table
	 * @param array $data
	 * @param array $where
	 * @return bool
	 */
	public function update($table, $data, $where)
	{
		/* creates the sql where clause */
		list($tables, $where, $values) = $this->_buildWhere(array($table => $where));
	
		/* extract fields and values and quotes values */
		$fields = array();
		foreach ($data as $field => $value) {
			$fields[] = $field . '=?';
		}
		$fields = implode(', ', $fields);
		
		/* statement params */
		$params = array_merge(array_values($data), $values);

		/* builds the sql string */
		$sql = 'UPDATE ' . implode(', ', $tables) . ' SET ' . $fields . $where;
	
		/* creates and executes the statement */
		$stmt = $this->pdo->prepare($sql);
		return $stmt->execute($params);
	}
	
	/**
	 * Deletes rows
	 *
	 * @see Db::buildWhere()
	 * @param array|string $tables
	 * @param array $where OPTIONAL
	 * @return bool
	 */
	public function delete($tables, $where = array())
	{
		/* creates the sql where clause */
		list($tables, $where, $values) = $this->_buildWhere($tables, $where);
		
		/* sql string */
		$sql = 'DELETE FROM ' . implode(', ', $tables) . $where;
		
		/* creates and executes the statement */
		$stmt = $this->pdo->prepare($sql);
		return $stmt->execute($values);
	}
	
	/**
	 * Buids and executes a SELECT query
	 *
	 * @see Db::buildWhere()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @param string|array $fields OPTIONAL
	 * @return PDOStatement
	 */
	protected function _executeSelect($tables, $where = null, $orderBy = '', $limit = '', $fields = '*')
	{
		/* creates the sql where clause */
		list($tables, $where, $values) = $this->_buildWhere($tables, $where);
	
		/* ORDER BY */
		if (!empty($orderBy)) {
			$orderBy = ' ORDER BY ' . $orderBy;
		}
	
		/* LIMIT */
		if (!empty($limit)) {
			$limit = ' LIMIT ' . $limit;
		}
		
		/* fields */
		if (is_array($fields)) {
			$fields = implode(', ', $fields);
		}
	
		/* build the sql string */
		$sql = 'SELECT ' . $fields . ' FROM ' . implode(', ', $tables) 
			 . $where . $orderBy . $limit;
		
		/* creates and executes the pdo statement */	 
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($values);
		
		return $stmt;
	}
	
	/**
	 * Builds an sql where clause
	 * 
	 * $where can ben an array where keys are field's name and values are field's value.
	 * $where can also be an sql condition string
	 * 
	 * Possible situations:
	 *
	 *  - $tables is an array ($where = null):
	 *    Allow to select data from multiple tables
	 *    keys will be treated as tables name. values
	 *    must be an array like $where (see below).
	 *    Example:
	 *     _buildWhere(array('table' => array('field1' => 'value1')));
	 *     SELECT * FROM table WHERE table.field1 = 'value1'
	 *
	 *  - $tables is a string ($where is needed):
	 *    Select data from one table. $where defines
	 *    condition(s). Example:
	 *     _buildWhere('table', array('field1' => 'value1'));
	 *     SELECT * FROM table WHERE table.field1 = 'value1'
	 *
	 * @param string|array $tables
	 * @param array|string $where OPTIONAL
	 * @param string $operator OPTIONAL (default ' AND ')
	 * @return array
	 */
	protected function _buildWhere($tables, $where = null, $operator = ' AND ')
	{
		$sql = '';
		
		/* if table is a string, transform it to an array */
		if (!is_array($tables)) {
			/* $where has not been set */
			if ($where === null) {
				$where = array();
			}
			$tables = array($tables => $where);
		}
		
		/* creates the sql condition for each key/value pair */
		$conditions = array();
		$values = array();
		foreach ($tables as $table => $fields) {
			if (is_array($fields)) {
				foreach ($fields as $field => $value) {
					/* escapes the value */	
					if (!is_array($value)) {
						$values[] = $value;
						$value = '?';
					} else {
						$value = $value[0];
					}
					$conditions[] = "${table}.${field}=$value";
				}
			} else {
				$conditions[] = (string) $fields;
			}
		}
		if (count($conditions)) {
			$sql = ' WHERE ' . implode($operator, $conditions);
		}
		
		return array(array_keys($tables), $sql, $values);
	}
}

