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

/** Atomik_Db_Query_Generator_Interface */
require_once 'Atomik/Db/Query/Generator/Interface.php';

/** Atomik_Db_Query_Generator */
require_once 'Atomik/Db/Query/Generator.php';

/** Atomik_Db_Query_Expr */
require_once 'Atomik/Db/Query/Expr.php';

/**
 * Used to generate and execute SQL queries
 *
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Query extends Atomik_Db_Query_Expr
{
    const _AND = ' AND ';
    const _OR = ' OR ';
    
	/** @var Atomik_Db_Instance */
	protected $_instance;
	
	/** @var PDO */
	protected $_pdo;
	
	/** @var Atomik_Db_Query_Generator_Interface */
	protected $_generator;
	
	/** @var string */
	protected $_rawSql;
	
	/** @var array */
	protected $_info;
	
	/** @var int */
	protected $_fetchMode = PDO::FETCH_ASSOC;
	
	/**
	 * Shortcut to create a new Atomik_Db_Query_Expr object
	 * 
	 * @see Atomik_Db_Query_Expr
	 * @param	string					$value
	 * @return	Atomik_Db_Query_Expr
	 */
	public static function expr($value)
	{
		return new Atomik_Db_Query_Expr($value);
	}
	
	/**
	 * Creates a new instance
	 * 
	 * @return Atomik_Db_Query
	 */
	public static function create(Atomik_Db_Instance $instance = null)
	{
		if ($instance === null) {
			$instance = Atomik_Db::getInstance();
		}
		return new self($instance);
	}
	
	/**
	 * Constructor
	 */
	public function __construct(Atomik_Db_Instance $instance)
	{
		$this->reset();
		
		$this->_instance = $instance;
		$this->_generator = $instance->getAdapter()->getQueryGenerator();
	}
	
	/**
	 * Resets the query
	 * @return	Atomik_Db_Query
	 */
	public function reset()
	{
		$this->_statement = null;
		$this->_info = array(
			'fields' 	=> array(),
			'from'		=> array(),
			'table'		=> null,
			'join'		=> array(),
			'where'		=> array(),
			'groupBy'	=> array(),
			'having'	=> array(),
			'orderBy'	=> array(),
			'limit'		=> null,
			'data'		=> array(),
			'params'	=> array()
		);
		
		return $this;
	}
	
	/**
	 * Returns the associated instance
	 * 
	 * @return Atomik_Db_Instance
	 */
	public function getInstance()
	{
		return $this->_instance;
	}
	
	/**
	 * Returns the sql generator
	 * 
	 * @return Atomik_Db_Query_Generator_Interface
	 */
	public function getGenerator()
	{
		return $this->_generator;
	}
	
	/**
	 * Sets the PDOStatement fetch mode
	 * 
	 * @param int $mode
	 */
	public function setFetchMode($mode)
	{
	    $this->_fetchMode = $mode;
	}
	
	/**
	 * Returns the fetch mode of the PDOStatement
	 * 
	 * @return int
	 */
	public function getFetchMode()
	{
	    return $this->_fetchMode;
	}
	
	/**
	 * Uses a raw sql query
	 * 
	 * @param 	string	$sqlString
	 * @return	Atomik_Db_Query
	 */
	public function sql($sqlString)
	{
		$this->_rawSql = $sqlString;
		return $this;
	}
	
	/**
	 * Creates a SELECT statement
	 * 
	 * Fields to select can be specified as an array or as arguments of the method
	 * 
	 * @param	string|array	$fields
	 * @param	args			...
	 * @return	Atomik_Db_Query
	 */
	public function select($fields = null)
	{
		if (!is_array($fields)) {
			$fields = func_get_args();
			if (count($fields) == 0 || $fields[0] === null) {
				$fields = array('*');
			}
		}
		
		$this->_rawSql = null;
		$this->_info['fields'] = array_merge($this->_info['fields'], $fields);
		
		return $this;
	}
	
	public function clearSelect()
	{
		$this->_info['fields'] = array();
		return $this;
	}
	
	/**
	 * Creates a SELECT COUNT() statement
	 * 
	 * @param 	string			$field
	 * @return 	Atomik_Db_Query
	 */
	public function count($field = '*')
	{
		$this->_rawSql = null;
		$this->_info['fields'] = array(sprintf('COUNT(%s)', $field));
		return $this;
	}
	
	/**
	 * Specifies the FROM part of a query
	 * 
	 * Tables can be specified as an array with table name as keys and their alias as values
	 * or only the table name as value.
	 * Otherwise, the table name and its alias (optional) can be specified as argument of the
	 * method. from() can be called more than one time.
	 * 
	 * @param	string|array	$table
	 * @param 	string			$alias
	 * @return	Atomik_Db_Query
	 */
	public function from($table, $alias = null)
	{
		if (is_array($table)) {
			foreach ($table as $key => $value) {
				if (is_int($key)) {
					$this->from($value);
				} else {
					$this->from($key, $value);
				}
			}
			return $this;
		}
		
		$this->_info['from'][] = array(
			'table' => $this->_formatTableName($table), 
			'alias' => $alias
		);
		
		return $this;
	}
	
	public function clearFrom()
	{
		$this->_info['from'] = array();
		return $this;
	}
	
	/**
	 * Specifies the JOIN part of a SELECT statement
	 * 
	 * @TODO implement join()
	 * @return	Atomik_Db_Query
	 */
	public function join($table, $on, $alias = null, $type = 'INNER')
	{
		$this->_info['join'][] = array(
			'table' => $this->_formatTableName($table), 
			'on' => $on, 
			'alias' => $alias, 
			'type' => strtoupper($type)
	    );
	    
		return $this;
	}
	
	public function clearJoin()
	{
		$this->_info['join'] = array();
		return $this;
	}
	
	/**
	 * Specifies the WHERE part
	 * 
	 * Possible arguments:
	 *  - where(string): a raw sql string
	 *  - where(string, string...): the first string is the sql string, following args are parameters (see PDO and params in prepare())
	 *  - where(string, array): the first arg is the sql string, the second arg is an array of parameters
	 *  - where(array): an array where keys are field name and their value the value that it should be equal to
	 * 
	 * It can be called multiple time. Each condition will be concatenate using AND
	 * 
	 * @return	Atomik_Db_Query
	 */
	public function where()
	{
		$args = func_get_args();
		$this->_info['where'][] = $this->_computeCondition($args);
		return $this;
	}
	
	/**
	 * Same as where() but will be concatenante using OR
	 * 
	 * @see Atomik_Db_Query::where()
	 * @return	Atomik_Db_Query
	 */
	public function orWhere()
	{
		$args = func_get_args();
		$this->_info['where'][] = $this->_computeCondition($args, self::_OR);
		return $this;
	}
	
	public function clearWhere()
	{
		$this->_info['where'] = array();
		return $this;
	}
	
	/**
	 * Specifies the GROUP BY part
	 * 
	 * Fields can be specified as an array of as arguments of the method
	 * 
	 * @param	string|array	$fields
	 * @param	args			...
	 * @return	Atomik_Db_Query
	 */
	public function groupBy($fields = null)
	{
		if (!is_array($fields)) {
			$fields = func_get_args();
		}
		$this->_info['groupBy'] = array_merge($this->_info['groupBy'], $fields);
		return $this;
	}
	
	public function clearGroupBy()
	{
		$this->_info['groupBy'] = array();
		return $this;
	}
	
	/**
	 * Specifies the HAVING part of GROUP BY
	 * 
	 * Works the same as where()
	 * 
	 * @see Atomik_Db_Query::where()
	 * @return	Atomik_Db_Query
	 */
	public function having()
	{
		$args = func_get_args();
		$this->_info['having'][] = $this->_computeCondition($args);
		return $this;
	}
	
	/**
	 * Same as having() but with OR
	 * 
	 * @see Atomik_Db_Query::having()
	 * @see Atomik_Db_Query::orWhere()
	 * @return	Atomik_Db_Query
	 */
	public function orHaving()
	{
		$args = func_get_args();
		$this->_info['having'][] = $this->_computeCondition($args, self::_OR);
		return $this;
	}
	
	public function clearHaving()
	{
		$this->_info['having'] = array();
		return $this;
	}
	
	/**
	 * Specifies the ORDER BY part
	 * 
	 * Fields can be specified as an array following this structure:
	 * 	array(fieldName, fieldName2 => direction)
	 * Otherwise it can be specified as argument of the method.
	 * 
	 * @param	string|array	$field		If the value contain ASC, DESC or a comma, it will be considered as a custom order by statement
	 * @param 	string			$direction
	 * @return	Atomik_Db_Query
	 */
	public function orderBy($field, $direction = null)
	{
		if (is_array($field)) {
			foreach ($field as $key => $value) {
				if (is_int($key)) {
					$this->orderBy($value);
				} else {
					$this->orderBy($key, $value);
				}
			}
			return $this;
			
		} else if (is_string($field)) {
			if (strpos($field, ',') !== false || preg_match('/(.+)\s+(ASC|DESC)/', $field, $matches)) {
				// use custom sql string
				$this->_info['orderBy'] = $field;
				return $this;
			}
		}
		
		$this->_info['orderBy'][$field] = $direction;
		return $this;
	}
	
	public function clearOrderBy()
	{
		$this->_info['orderBy'] = array();
		return $this;
	}
	
	/**
	 * Specifies the LIMIT part
	 * 
	 * Arguments can be:
	 *  - limit(sqlString)
	 *  - limit(length)
	 *  - limit(offset, length)
	 *  - limit(array(length))
	 *  - limit(array(offset, length))
	 * 
	 * @return	Atomik_Db_Query
	 */
	public function limit($limit)
	{
		if ($limit === false) {
			$this->_info['limit'] = null;
			return $this;
		}
		
		if (is_string($limit)) {
			$args = explode(',', $limit);
		} else if (is_array($limit)) {
			$args = $limit;
		} else {
			$args = func_get_args();
		}
		
		$offset = 0;
		$length = $args[0];
		
		if (count($args) == 2) {
			$offset = $args[0];
			$length = $args[1];
		}
		
		$this->_info['limit'] = array(
			'length' => (int) $length,
			'offset' => (int) $offset
		);
		
		return $this;
	}
	
	/**
	 * Resets params
	 * 
	 * @param 	array $params
	 * @return	Atomik_Db_Query
	 */
	public function setParams($params)
	{
		$this->_info['params'] = (array) $params;
		return $this;
	}
	
	/**
	 * Sets a param value
	 * 
	 * @param	string|int	$index
	 * @param 	string		$value
	 * @return	Atomik_Db_Query
	 */
	public function setParam($index, $value)
	{
		$this->_info['params'][$index] = $value;
		return $this;
	}
	
	/**
	 * Returns the parameters associated to the query
	 * 
	 * @return array
	 */
	public function getParams()
	{
		return $this->_info['params'];
	}
	
	/**
	 * Returns query information
	 * 
	 * @param	string	$key	To retreive only one type of information
	 * @return 	array
	 */
	public function getInfo($key = null)
	{
		if ($key === null) {
			return $this->_info;
		}
		return $this->_info[$key];
	}
	
	/**
	 * Returns the query's info as an array
	 * 
	 * @return array
	 */
	public function toArray()
	{
		return $this->_info;
	}
	
	/**
	 * Returns a unique hash representing this query
	 * 
	 * @return string
	 */
	public function toHash()
	{
		return md5($this->toSql() . implode(',', $this->getParams()));
	}
	
	/**
	 * Returns the query as an SQL string
	 * 
	 * @return string
	 */
	public function toSql()
	{
		if ($this->_rawSql !== null) {
			return $this->_rawSql;
		}
		return $this->getGenerator()->generate($this);
	}
	
	/**
	 * Returns the conditions (WHERE part) as an SQL string
	 * 
	 * @return string
	 */
	public function getConditionString()
	{
		return $this->_concatConditions($this->_info['where']);
	}
	
	/**
	 * Executes the request against a db instance
	 * 
	 * @return PDOStatement
	 */
	public function execute()
	{
	    if (($stmt = $this->_instance->query($this->toSql(), $this->getParams())) === false) {
	        return false;
	    }
	    
		$stmt->setFetchMode($this->_fetchMode);
		return $stmt;
	}
	
	/**
	 * PHP magic method. Returns the query as an SQL string.
	 * 
	 * @see Atomik_Db_Query::toSql()
	 * @return string
	 */
	public function __toString()
	{
		return $this->toSql();
	}
	
	/**
	 * Returns the prefixed table name
	 * 
	 * @param	string	$tableName
	 * @return 	string
	 */
	protected function _formatTableName($tableName)
	{
		return $this->_instance->getTablePrefix() . $tableName;
	}
	
	/**
	 * Returns information about a condition. Also extract params.
	 * 
	 * @param	array	$args
	 * @param 	string	$operator
	 * @return 	array
	 */
	protected function _computeCondition($args, $operator = self::_AND)
	{
	    $sql = array_shift($args);
	    $params = count($args) && is_array($args[0]) ? $args[0] : $args;
	    
		list($sql, $params) = $this->_buildConditionString($sql, $params);
		$this->_info['params'] = array_merge($this->_info['params'], $params);
		
		return array(
			'sql' 		=> $sql,
			'operator' 	=> $operator
		);
	}
	
	/**
	 * Concatenates a condition array
	 * 
	 * @param	array	$array
	 * @return 	string
	 */
	protected function _concatConditions($array)
	{
		$sql = '';
		
		for ($i = 0, $c = count($array); $i < $c; $i++) {
			if ($i > 0) {
				$sql .= $array[$i]['operator'];
			}
			$sql .= $array[$i]['sql'];
		}
		
		return trim($sql);
	}
	
	/**
	 * Builds an SQL condition string
	 * 
	 * @param	string|array	$fields		A raw sql string with params if needed or an array of the form fieldName => value
	 * @param 	array			$params		Parameters (see PDO and prepare())
	 * @param 	string			$operator	The operator used to implode conditions
	 * @return 	array						array(fields, params)
	 */
	protected function _buildConditionString($fields, $params = array(), $operator = self::_AND)
	{
		if (is_string($fields)) {
			$sql = $fields;
			$finalParams = array();
			
			foreach ($params as $name => $value) {
				if ($value instanceof Atomik_Db_Query_Expr) {
					$subject = is_int($name) ? '?' : $name;
					$sql = substr_replace($sql, $value->__toString(), strpos($sql, $subject), strlen($subject));
				} else {
					$finalParams[$name] = $value;
				}
			}
			
			return array($sql, $finalParams);
		}
		
		$sql = '';
		$conditions = array();
		
		foreach ($fields as $field => $value) {
			if (is_numeric($field)) {
				$conditions[] = (string) $value;
				continue;
			}
			
			if ($value instanceof Atomik_Db_Query_Expr) {
				$value = $value->__toString();
			} else {
				$params[] = $value;
				$value = '?';
			}
			$conditions[] = "$field = $value";
		}
		
		if (count($conditions)) {
			$sql = implode($operator, $conditions);
		}
		
		return array($sql, $params);
	}
}
