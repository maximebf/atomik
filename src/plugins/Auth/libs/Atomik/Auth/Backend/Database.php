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

/** Atomik_Db */
require_once 'Atomik/Db.php';

/**
 * Store users in a database
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_Backend_Database implements Atomik_Auth_Backend_Interface
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
	 * @return Atomik_Auth_Locator_Database
	 */
	public function getLocator()
	{
	    /** Atomik_Auth_Locator_Database */
	    require_once 'Atomik/Auth/Locator/Database.php';
	    return new Atomik_Auth_Locator_Database(
	        $this->db, $this->tableName, $this->idColumn, 
	        $this->userColumn, $this->passwordColumn, $this->rolesColumn);
	}
	
	/**
	 * Checks whether a user exists with the specified credentials
	 * 
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	public function authentify($username, $password)
	{
		$user = $this->db->find($this->tableName, array(
			$this->userColumn => $username,
			$this->passwordColumn => md5($password)
		));
		
		if ($user !== false) {
			return $user[$this->idColumn];
		}
		return false;
	}
}