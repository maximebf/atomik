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

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Query
{
	const ASC = 'asc';
	const DESC = 'desc';
	
	/**
	 * @var string
	 */
	public $rawQuery;
	
	/**
	 * @var Atomik_Model_Builder
	 */
	public $from;
	
	/**
	 * @var array
	 */
	public $where = array();
	
	/**
	 * @var string
	 */
	public $orderByField;
	
	/**
	 * @var string
	 */
	public $orderByDirection = 'ASC';
	
	/**
	 * @var int
	 */
	public $limitOffset = 0;
	
	/**
	 * @var int
	 */
	public $limitLength;
	
	/**
	 * Constructor
	 * 
	 * @param	string	$rawQuery
	 */
	public function __construct($rawQuery = null)
	{
		$this->rawQuery = $rawQuery;
	}
	
	/**
	 * Uses a raw query
	 * 
	 * @param	string	$string
	 */
	public function raw($string)
	{
		$this->rawQuery = $string;
	}
	
	/**
	 * Checks if the request uses a raw query
	 * 
	 * @return bool
	 */
	public function isRaw()
	{
		return $this->rawQuery !== null;
	}
	
	/**
	 * Sets from which model to select data
	 * 
	 * @param	string|Atomik_Model_Builder|Atomik_Model	$builder
	 * @return 	Atomik_Model_Query
	 */
	public function from($builder)
	{
		$this->rawQuery = null;
		$this->from = Atomik_Model_Builder_Factory::get($builder);
		return $this;
	}
	
	/**
	 * Sets one or many conditions
	 * 
	 * @param	string|array	$field
	 * @param	string			$value
	 * @return 	Atomik_Model_Query
	 */
	public function where($field, $value = null)
	{
		$this->rawQuery = null;
		if (is_array($field)) {
			$this->where = array_merge($this->where, $field);
			return $this;
		}
		
		$this->where[$field] = $value;
		return $this;
	}
	
	/**
	 * Orders the result depending on a field
	 * 
	 * @param	string	$field
	 * @param	string	$direction
	 * @return 	Atomik_Model_Query
	 */
	public function orderBy($field, $direction = self::ASC)
	{
		$this->rawQuery = null;
		$this->orderByField = $field;
		$this->orderByDirection = $direction;
		return $this;
	}
	
	/**
	 * Limits the number of results
	 * 
	 * @param	int		$length
	 * @param 	int		$offset
	 * @return 	Atomik_Model_Query
	 */
	public function limit($length, $offset = 0)
	{
		$this->rawQuery = null;
		$this->limitLength = $length;
		$this->limitOffset = $offset;
		return $this;
	}
}