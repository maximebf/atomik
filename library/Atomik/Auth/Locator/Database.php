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

/** Atomik_Auth_Locator_Interface */
require_once 'Atomik/Auth/Locator/Interface.php';

/** Atomik_Auth_User */
require_once 'Atomik/Auth/User.php';

/** Atomik_Db */
require_once 'Atomik/Db.php';

/**
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_Locator_Database implements Atomik_Auth_Locator_Interface
{
	/** @var Atomik_Db_Instance */
	public $db;
	
	/** @var string */
	public $tableName;
	
	/** @var string */
	public $idColumn;
	
	/** @var string */
	public $userColumn;
	
	/** @var string */
	public $passwordColumn;
	
	/** @var string */
	public $rolesColumn;
	
	/**
	 * @param string|Atomik_Db_Instance $dbInstance
	 * @param string $tableName
	 * @param string $idColumn
	 * @param string $userColumn
	 * @param string $passwordColumn
	 * @param string $rolesColumn
	 */
	public function __construct($dbInstance, $tableName = 'users', 
	    $idColumn = 'id', $userColumn = 'username', $passwordColumn = 'password',
	    $rolesColumn = 'roles')
	{
	    $this->db = Atomik_Db::getInstance($dbInstance);
		$this->tableName = $tableName;
		$this->idColumn = $idColumn;
		$this->userColumn = $userColumn;
		$this->passwordColumn = $passwordColumn;
		$this->rolesColumn = $rolesColumn;
	}
	
	/**
	 * Returns the object for the specified username
	 * 
	 * @param string $id
	 * @return object
	 */
	public function find($id)
	{
		return $this->db->find($this->tableName, array($this->idColumn => $id));
	}
}