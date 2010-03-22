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

/**
 * Used to get a user object
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_User_Locator_Model implements Atomik_Auth_User_Locator_Interface
{
	/** @var string */
	protected $_modelName;
	
	/** @var string */
	protected $_userField;
	
	/**
	 * @param string $modelName
	 */
	public function __construct($modelName = null, $userField = 'username')
	{
	    $this->_modelName = $modelName;
	    $this->_userField = $userField;
	}
	
	/**
	 * Sets the model name to use for user objects
	 * 
	 * @param string $name
	 */
	public function setModelName($name)
	{
    	$this->_modelName = $name;
	}
	
	/**
	 * Returns the model name used for user objects
	 * 
	 * @return string
	 */
	public function getModelName()
	{
		return $this->_modelName;
	}
	
	/**
	 * @param string $field
	 */
	public function setUserField($field)
	{
	    $this->_userField = $field;
	}
	
	/**
	 * @return string
	 */
	public function getUserField()
	{
	    return $this->_userField;
	}
	
	/**
	 * Returns the object for the specified username
	 * 
	 * @param string $username
	 * @return Atomik_Auth_User_Interface
	 */
	public function find($username)
	{
		if ($this->_modelName === null) {
			require_once 'Atomik/Auth/Exception.php';
			throw new Atomik_Auth_Exception('A model name must be specified for Atomik_Auth_User_Locator_Model');
		}
		return Atomik_Model_Query::find($this->_modelName, array($this->_userField => $username));
	}
}