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
 * @subpackage Model
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Model_Query_Filter_Interface */
require_once 'Atomik/Model/Query/Filter/Interface.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Query_Filter_Expr implements Atomik_Model_Query_Filter_Interface
{
    /** @var string */
	protected $_expr;
	
	/**
	 * @param string $expr
	 */
	public function __construct($expr)
	{
		$this->_expr = $expr;
	}
	
	/**
	 * Sets a raw sql string that will be used in the where clause of the
	 * db query
	 * 
	 * @param string $expr
	 */
	public function setExpr($expr)
	{
		$this->_expr = $expr;
	}
	
	/**
	 * @return string
	 */
	public function getExpr()
	{
		return $this->_expr;
	}
	
	/**
	 * @see Atomik_Model_Query_Filter_Interface::getSqlAndParams()
	 */
	public function getSqlAndParams()
	{
		return array($this->_expr, array());
	}
}