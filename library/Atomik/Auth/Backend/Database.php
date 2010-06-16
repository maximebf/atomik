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
	
	/**
	 * Constructor
	 * 
	 * @param string $modelName
	 */
	public function __construct(Atomik_Db_Instance $db, $tableName = 'users', 
	    $idColumn = 'id', $userColumn = 'username', $passwordColumn = 'password')
	{
	    $this->db = $db;
		$this->tableName = $tableName;
		$this->idColumn = $idColumn;
		$this->userColumn = $userColumn;
		$this->passwordColumn = $passwordColumn;
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