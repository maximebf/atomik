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

/** Atomik_Db_Script_Interface */
require_once 'Atomik/Db/Script/Interface.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Script_Text implements Atomik_Db_Script_Interface
{
	/**
	 * @var string
	 */
	public $sql = '';
	
	/**
	 * Constructor
	 * 
	 * @param	string	$sql
	 */
	public function __construct($sql = '')
	{
		$this->sql = $sql;
	}
	
	/**
	 * Returns the sql text
	 * 
	 * @return string
	 */
	public function getSql()
	{
		return $this->sql;
	}
	
	/**
	 * @see Atomik_Db_Script_Text::getSql()
	 */
	public function __toString()
	{
		return $this->getSql();
	}
}