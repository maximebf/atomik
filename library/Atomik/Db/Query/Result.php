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

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Query_Result implements Iterator, ArrayAccess, Countable
{
	/**
	 * @var Atomik_Db_Query
	 */
	protected $_query;
	
	/**
	 * @var PDOStatement
	 */
	protected $_statement;
	
	/**
	 * @var array
	 */
	protected $_errorInfo;
	
	/**
	 * @var int
	 */
	protected $_columnCount = 0;
	
	/**
	 * @var int
	 */
	protected $_rowCount = 0;
	
	/**
	 * @var int
	 */
	protected $_index = -1;
	
	/**
	 * @var array
	 */
	protected $_rows = array();
	
	/**
	 * @var int
	 */
	protected $_fetchMode;
	
	/**
	 * @var int
	 */
	protected static $_defaultFetchMode = PDO::FETCH_BOTH;
	
	/**
	 * Sets the default fetch mode
	 * 
	 * @param int $fetchMode
	 */
	public static function setDefaultFetchMode($fetchMode)
	{
		self::$_defaultFetchMode = $fetchMode;
	}
	
	/**
	 * Returns the default fetch mode
	 * 
	 * @return int
	 */
	public static function getDefaultFetchMode()
	{
		return self::$_defaultFetchMode;
	}
	
	/**
	 * Constructo
	 * 
	 * @param	Atomik_Db_Query	$query
	 * @param	PDOStatement	$statement
	 */
	public function __construct(Atomik_Db_Query $query, PDOStatement $statement = null)
	{
		$this->_query = $query;
		if ($statement !== null) {
			$this->reset($statement);
		}
	}
	
	/**
	 * Destructor
	 */
	public function __destruct()
	{
		if ($this->_statement !== null) {
			$this->_statement->closeCursor();
		}
	}
	
	/**
	 * Resets the result with a new statement
	 * 
	 * @param	PDOStatement	$statement
	 */
	public function reset(PDOStatement $statement)
	{
		$this->_statement = $statement;
		$this->_errorInfo = $statement->errorInfo();
		$this->_columnCount = $statement->columnCount();
		$this->_rowCount = $statement->rowCount();
		$this->_index = -1;
		$this->_rows = array();
		
		$this->setFetchMode(self::getDefaultFetchMode());
	}
	
	/**
	 * Returns the query associated to this result
	 * 
	 * @return Atomik_Db_Query
	 */
	public function getQuery()
	{
		return $this->_query;
	}
	
	/**
	 * Returns the pdo statement associated to this query
	 * If the result is cached, null will be returned
	 * 
	 * @return PDOStatement
	 */
	public function getStatement()
	{
		return $this->_statement;
	}
	
	/**
	 * Checks if the result is cached
	 * 
	 * @return bool
	 */
	public function isCached()
	{
		return $this->_statement === null;
	}
	
	/**
	 * Re-executes the query and refreshes the result
	 */
	public function reload()
	{
		$this->_query->execute(null, true, $this);
	}
	
	/**
	 * Returns the error code
	 * 
	 * @return int
	 */
	public function errorCode()
	{
		return $this->_errorInfo[0];
	}
	
	/**
	 * Returns error information
	 * 
	 * @return array
	 */
	public function errorInfo()
	{
		return $this->_errorInfo;
	}
	
	/**
	 * Closes the cursor
	 */
	public function closeCursor()
	{
		if ($this->_statement !== null) {
			return $this->_statement->closeCursor();
		}
	}
	
	/**
	 * Returns the total number of rows
	 * 
	 * @return int
	 */
	public function rowCount()
	{
		return $this->_rowCount;
	}
	
	/**
	 * Returns the number of rows actually fetched
	 * 
	 * @return int
	 */
	public function fetchedRowCount()
	{
		return count($this->_rows);
	}
	
	/**
	 * Returns the number of columns
	 * 
	 * @return int
	 */
	public function columnCount()
	{
		return $this->_columnCount;
	}
	
	/**
	 * Sets the fetch mode
	 * Must be set before any fetch call
	 * 
	 * @see PDOStatement::setFetchMode()
	 * @param int $fetchMode
	 */
	public function setFetchMode($fetchMode)
	{
		if ($this->_statement !== null) {
			if ($this->_index >= 0) {
				require_once 'Atomik/Db/Query/Exception.php';
				throw new Atomik_Db_Query_Exception('Fetch mode can only be set before any row have been fetched');
			}
			$args = func_get_args();
			$this->_fetchMode = $fetchMode;
			return call_user_func_array(array($this->_statement, 'setFetchMode'), $args);
		}
		
		require_once 'Atomik/Db/Query/Exception.php';
		throw new Atomik_Db_Query_Exception('Fetch mode can only be set when the query is not cached');
	}
	
	/**
	 * Returns the fetch mode
	 * 
	 * @return int
	 */
	public function getFetchMode()
	{
		return $this->_fetchMode;
	}
	
	/**
	 * Fetches one row
	 * 
	 * @return mixed False if there's no more rows to fetch
	 */
	public function fetch($index = null)
	{
		if ($index !== null) {
			$this->_index = $index;
		} else {
			$this->_index++;
		}
		
		if (isset($this->_rows[$this->_index])) {
			return $this->_rows[$this->_index];
		}
		
		if ($this->_statement !== null) {
			if ($index !== null) {
				$row = $this->_statement->fetch($this->getFetchMode(), PDO::FETCH_ORI_ABS, $this->_index);
			} else {
				$row = $this->_statement->fetch();
			}
			if ($row === false) {
				$this->_statement = null;
				return false;
			}
			$this->_rows[$this->_index] = $row;
			return $row;
		}
		
		return false;
	}
	
	/**
	 * Fetches all rows
	 * 
	 * @return array
	 */
	public function fetchAll()
	{
		if ($this->_statement !== null) {
			$this->_rows = $this->_statement->fetchAll();
			$this->_statement = null;
			$this->_index = count($this->_rows) - 1;
		}
		return $this->_rows;
	}
	
	/**
	 * Fetches one column from the next row
	 * 
	 * @param 	int		$columnNumber
	 * @return 	mixed
	 */
	public function fetchColumn($columnNumber = 0)
	{
		// full fetch needed because data is cached
		$row = $this->fetch();
		
	}
	
	/**
	 * Move to the next row
	 */
	public function nextRowset()
	{
		if ($this->_statement !== null) {
			$this->_statement->nextRowset();
		}
		$this->_index++;
	}
	
	/**
	 * Returns the result as an array
	 * 
	 * @return array
	 */
	public function toArray()
	{
		return $this->fetchAll();
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  Iterator
	 * ------------------------------------------------------------------------------------------ */
	
	public function current()
	{
		if (!isset($this->_rows[$this->_index])) {
			return false;
		}
		return $this->_rows[$this->_index];
	}
	
	public function key()
	{
		return $this->_index;
	}
	
	public function next()
	{
		$this->fetch();
	}
	
	public function rewind()
	{
		$this->_index = -1;
		$this->fetch();
	}
	
	public function valid()
	{
		return $this->_index < $this->_rowCount;
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  Countable
	 * ------------------------------------------------------------------------------------------ */
	
	public function count()
	{
		return $this->_rowCount;
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  ArrayAccess
	 * ------------------------------------------------------------------------------------------ */
	
	public function offsetExists($index)
	{
		if ($this->_statement !== null) {
			return $index < $this->_columnCount;
		}
		return $index < count($this->_rows);
	}
	
	public function offsetGet($index)
	{
		return $this->fetch($index);
	}
	
	public function offsetSet($index, $model)
	{
	}
	
	public function offsetUnset($index)
	{
	}
}