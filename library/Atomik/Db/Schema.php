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

/** Atomik_Db_Schema_Table */
require_once 'Atomik/Db/Schema/Table.php';

/** Atomik_Db_Schema_Generator */
require_once 'Atomik/Db/Schema/Generator.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Schema
{
	protected $_instance;
	
	protected $_generator;
	
	protected $_tables = array();
	
	public static function create(Atomik_Db_Instance $instance = null)
	{
		if ($instance === null) {
			$instance = Atomik_Db::getInstance();
		}
		return new self($instance);
	}
	
	public function __construct(Atomik_Db_Instance $instance)
	{
		$this->_instance = $instance;
		$this->_generator = $instance->getAdapter()->getSchemaGenerator();
	}
	
	public function getInstance()
	{
		return $this->_instance;
	}
	
	public function getGenerator()
	{
		return $this->_generator;
	}
	
	public function createTable($name, $columns = array(), $indexes = array())
	{
	    $table = new Atomik_Db_Schema_Table($name, $columns, $indexes);
	    $this->addTable($table);
	    return $table;
	}
	
	public function addTable(Atomik_Db_Schema_Table $table)
	{
	    $this->_tables[$table->getName()] = $table;
	}
	
	public function hasTable($name)
	{
	    return isset($this->_tables[$name]);
	}
	
	public function getTable($name)
	{
	    if (!isset($this->_tables[$name])) {
	        return null;
	    }
	    return $this->_tables[$name];
	}
	
	public function getTables()
	{
	    return $this->_tables;
	}
	
	public function toSql()
	{
		return $this->_generator->generateSchema($this);
	}
}