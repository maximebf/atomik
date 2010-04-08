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

/** Atomik_Db_Schema_Column */
require_once 'Atomik/Db/Schema/Column.php';

/** Atomik_Db_Schema_Index */
require_once 'Atomik/Db/Schema/Index.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Schema_Table
{
    /** @var string */
	protected $_name;
	
	/** @var array of Atomik_Db_Schema_Colunm */
	protected $_columns = array();
	
	/** @var array of Atomik_Db_Schema_Index */
	protected $_indexes = array();
	
	/** @var Atomik_Db_Schema_Colunm */
	protected $_primaryKey;
	
	public function __construct($name, $columns = array(), $indexes = array())
	{
		$this->_name = $name;
		array_map(array($this, 'addColumn'), $columns);
		array_map(array($this, 'addIndex'), $indexes);
	}
	
	/**
	 * Sets the table's name
	 * 
	 * @param string $name
	 */
	public function setName($name)
	{
	    $this->_name = $name;
	}
	
	/**
	 * Returns the table's name
	 * 
	 * @return string
	 */
	public function getName()
	{
	    return $this->_name;
	}
	
	public function createColumn($name, Atomik_Db_Type_Abstract $type, $options = array())
	{
		$column = new Atomik_Db_Schema_Column($name, $type, $options);
		$this->addColumn($column);
		return $column;
	}
	
	public function addColumn(Atomik_Db_Schema_Column $column)
	{
	    $this->_columns[$column->getName()] = $column;
	}
	
	public function hasColumn($name)
	{
	    return isset($this->_columns[$name]);
	}
	
	public function getColumn($name)
	{
	    if (!isset($this->_columns[$name])) {
	        return null;
	    }
	    return $this->_columns[$name];
	}
	
	public function getColumns()
	{
	    return $this->_columns;
	}
	
	public function setPrimaryKey(Atomik_Db_Schema_Column $column)
	{
		$this->_primaryKey = $column;
	}
	
	public function getPrimaryKey()
	{
	    return $this->_primaryKey;
	}
	
	public function createIndex($name, $column)
	{
	    if (is_string($column)) {
	        $column = $this->getColumn($column);
	    }
	    $index = new Atomik_Db_Schema_Index($name, $column);
	    $this->addIndex($index);
	    return $index;
	}
	
	public function addIndex(Atomik_Db_Schema_Index $index)
	{
	    $this->_indexes[$index->getName()] = $index;
	}
	
	public function hasIndex($name)
	{
	    return isset($this->_indexes[$name]);
	}
	
	public function getIndex($name)
	{
	    if (!isset($this->_indexes[$name])) {
	        return null;
	    }
	    return $this->_indexes[$name];
	}
	
	public function getIndexes()
	{
	    return $this->_indexes;
	}
}