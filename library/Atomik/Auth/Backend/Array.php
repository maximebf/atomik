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
 * Store users in an array
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_Backend_Array implements Atomik_Auth_Backend_Interface
{
	/**
	 * array(username => password)
	 * 
	 * @var array
	 */
	public $users = array();
	
	/**
	 * Constructor
	 * 
	 * @param array $users
	 */
	public function __construct($users = array())
	{
		$this->users = $users;
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
		if(isset($this->users[$username]) && $this->users[$username] == $password) {
			return $username;
		}
		return false;
	}
}