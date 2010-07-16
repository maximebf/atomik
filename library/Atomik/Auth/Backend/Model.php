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

/**
 * Store users in models
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_Backend_Model implements Atomik_Auth_Backend_Interface
{
	/**
	 * @var string
	 */
	public $modelName;
	
	/**
	 * @var string
	 */
	public $userField = 'username';
	
	/**
	 * @var string
	 */
	public $passwordField = 'password';
	
	/**
	 * Constructor
	 * 
	 * @param string $modelName
	 */
	public function __construct($modelName = 'Atomik_Auth_User', $userField = 'username', $passwordField = 'password')
	{
		$this->modelName = $modelName;
		$this->userField = $userField;
		$this->passwordField = $passwordField;
	}
	
	/**
	 * @return Atomik_Auth_Locator_Model
	 */
	public function getLocator()
	{
	    /** Atomik_Auth_Locator_Model */
	    require_once 'Atomik/Auth/Locator/Model.php';
	    return new Atomik_Auth_Locator_Model($this->modelName);
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
		$user = Atomik_Model_Query::find($this->modelName, array(
			$this->userField => $username,
			$this->passwordField => md5($password)
		));
		
		if ($user !== null) {
			return $user->getProperty($user->getDescriptor()->getIdentifierField()->getName());
		}
		return false;
	}
}