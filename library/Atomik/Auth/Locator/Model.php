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

/** Atomik_Model_Query */
require_once 'Atomik/Model/Query.php';

/**
 * Used to get a user object
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_Locator_Model implements Atomik_Auth_Locator_Interface
{
	/** @var string */
	protected $_modelName;
	
	/**
	 * @param string $modelName
	 */
	public function __construct($modelName = 'Atomik_Auth_User_Model')
	{
	    $this->_modelName = $modelName;
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
	 * Returns the object for the specified username
	 * 
	 * @param string $id
	 * @return object
	 */
	public function find($id)
	{
		if ($this->_modelName === null) {
			require_once 'Atomik/Auth/Exception.php';
			throw new Atomik_Auth_Exception('A model name must be specified for Atomik_Auth_Locator_Model');
		}
		return Atomik_Model_Query::find($this->_modelName, $id);
	}
}