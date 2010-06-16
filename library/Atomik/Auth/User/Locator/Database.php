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
 * @subpackage Auth
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Auth_User_Locator_Interface */
require_once 'Atomik/Auth/User/Locator/Interface.php';

/** Atomik_Db */
require_once 'Atomik/Db.php';

/**
 * Used to get a user object
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_User_Locator_Database implements Atomik_Auth_User_Locator_Interface
{
    /** @var Atomik_Db_Instance */
    protected $_db;
    
	/** @var string */
	protected $_tableName;
    
	/** @var string */
	protected $_idColumn;
	
	/**
	 * @param string $modelName
	 */
	public function __construct(Atomik_Db_Instance $db, $tableName = 'users', $idColumn = 'id')
	{
	    $this->_db = $db;
	    $this->_tableName = $tableName;
	    $this->_idColumn = $idColumn;
	}
	
	/**
	 * @param Atomik_Db_Instance $db
	 */
	public function setDb(Atomik_Db_Instance $db)
	{
	    $this->_db = $db;
	}
	
	/**
	 * @return Atomik_Db_Instance
	 */
	public function getDb()
	{
	    return $this->_db;
	}
	
	/**
	 * @param string $name
	 */
	public function setTableName($name)
	{
    	$this->_tableName = $name;
	}
	
	/**
	 * @return string
	 */
	public function getTableName()
	{
		return $this->_tableName;
	}
	
	/**
	 * @param string $name
	 */
	public function setIdColumn($name)
	{
	    $this->_idColumn = $name;
	}
	
	/**
	 * @return string 
	 */
	public function getIdColumn()
	{
	    return $this->_idColumn;
	}
	
	/**
	 * Returns the object for the specified username
	 * 
	 * @param string $id
	 * @return object
	 */
	public function find($id)
	{
		return $this->_db->find($this->_tableName, array($this->_idColumn => $id));
	}
}