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

/** Atomik_Model_Query_Filter_Field */
require_once 'Atomik/Model/Query/Filter/Field.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Query_Filter extends Atomik_Model_Query_Filter_Field
{
    /** @var string */
	protected $_operator;
	
	/**
	 * Creates a filter
	 * 
	 * @param string $filterName
	 * @param mixed $descriptor
	 * @param mixed $field
	 * @param string $value
	 * @return Atomik_Model_Query_Filter_Interface
	 */
	public static function factory($filterName, $descriptor, $field, $value)
	{
	    $operators = array(
	    	'Equal' => '=', 
	    	'NotEqual' => '!=', 
	    	'GreaterThan' => '>', 
	    	'LowerThan' => '<',
	        'GreaterEqThan' => '>=',
	        'LowerEqThan' => '<=');
	    
	    if (isset($operators[$filterName])) {
	        return new Atomik_Model_Query_Filter($descriptor, $field, $value, $operators[$filterName]);
	    }
	    
	    $className = 'Atomik_Model_Query_Filter_' . $filterName;
	    if (class_exists($className) && is_subclass_of($className, 'Atomik_Model_Query_Filter_Abstract')) {
	        return new $className($descriptor, $field, $value);
	    }
	    
        require_once 'Atomik/Model/Query/Exception.php';
	    throw new Atomik_Model_Query_Exception("Query filter '$filter' not found");
	}
	
	/**
	 * Creates an unparsed filter (expression)
	 * 
	 * @param string $expr
	 * @return Atomik_Model_Query_Filter_Expr
	 */
	public static function expr($expr)
	{
	    require_once 'Atomik/Model/Query/Filter/Expr.php';
	    return new Atomik_Model_Query_Filter_Expr($expr);
	}
	
	/**
	 * @param mixed $descriptor
	 * @param mixed $field
	 * @param string $value
	 * @param string $operator
	 */
	public function __construct($descriptor, $field, $value = null, $operator = '=')
	{
		parent::__construct($descriptor, $field, $value);
		$this->_operator = $operator;
	}
	
	/**
	 * Sets the sql operator to use between the two operands
	 * 
	 * @param string $op
	 */
	public function setOperator($op)
	{
		$this->_operator = $op;
	}
	
	/**
	 * Returns the sql operator
	 * 
	 * @return string
	 */
	public function getOperator()
	{
		return $this->_operator;
	}
	
	/**
	 * @see Atomik_Model_Query_Filter_Interface::apply()
	 */
	public function apply(Atomik_Db_Query $query)
	{
	    $where = sprintf('%s %s ?', $this->_getSqlColumn(), $this->_operator);
	    $query->where($where, $this->_value);
	}
}