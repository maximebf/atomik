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

/**
 * Store users in an array.
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_Locator_Array implements Atomik_Auth_Locator_Interface
{
	/** @var Atomik_Auth_Backend_Array */
	protected $_backend;
	
	/**
	 * @param Atomik_Auth_Backend_Array $backend
	 */
	public function __construct(Atomik_Auth_Backend_Array $backend)
	{
	    $this->_backend = $backend;
	}
	
	/**
	 * @param string $username
	 * @return Atomik_Auth_user
	 */
	public function find($username)
	{
		return $this->_backend->getUser($username);
	}
}