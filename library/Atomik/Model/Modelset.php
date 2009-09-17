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

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Modelset implements Iterator, ArrayAccess, Countable
{
	/**
	 * @var int
	 */
	protected $_pointer = 0;
	
	/**
	 * @var int
	 */
	protected $_count;
	
	/**
	 * @var Atomik_Model_Builder
	 */
	protected $_builder;
	
	/**
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * @var array
	 */
	protected $_models = array();
	
	/**
	 * Constructor
	 * 
	 * @param	Atomik_Model_Builder	$builder
	 * @param	array					$data
	 */
	public function __construct(Atomik_Model_Builder $builder, $data)
	{
		$this->_builder = $builder;
		$this->setData($data);
	}
	
	/**
	 * Sets the raw data
	 * 
	 * @param array $data
	 */
	public function setData($data)
	{
		$this->_data = $data;
		$this->_count = count($this->_data);
		$this->_pointer = 0;
		$this->_models = array();
	}
	
	/**
	 * Returns the raw data
	 * 
	 * @return array
	 */
	public function getData()
	{
		return $this->_data;
	}
	
	/**
	 * Returns the model at the specified index
	 * 
	 * @param	int		$index
	 * @return 	Atomik_Model
	 */
	public function item($index)
	{
		if ($index >= $this->_count) {
			return false;
		}
		
		if (!isset($this->_models[$index])) {
			$this->_models[$index] = $this->_builder->createInstance(
				$this->_data[$index],
				false
			);
		}
		
		return $this->_models[$index];
	}
	
	/**
	 * Returns an array of data
	 * 
	 * @param	array 	$fieldsToInclude Name of the fields to include
	 * @return 	array
	 */
	public function toDataArray($fieldsToInclude = null)
	{
		if ($fieldsToInclude === null) {
			return $this->_data;
		}
		
		$data = array();
		$fieldsToInclude = array_fill_keys((array) $fieldsToInclude, '');
		foreach ($this->_data as $item) {
			$data[] = array_intersect_key($item, $fieldsToInclude);
		}
		return $data;
	}
	
	public function toArray()
	{
        $this->current();
		while ($this->next()) {
			$this->current();
		}
		return $this->_models;
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  Iterator
	 * ------------------------------------------------------------------------------------------ */
	
	public function current()
	{
		return $this->item($this->_pointer);
	}
	
	public function key()
	{
		return $this->_pointer;
	}
	
	public function next()
	{
		$this->_pointer++;
		return $this->valid();
	}
	
	public function rewind()
	{
		$this->_pointer = 0;
	}
	
	public function valid()
	{
		return $this->_pointer < $this->_count;
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  Countable
	 * ------------------------------------------------------------------------------------------ */
	
	public function count()
	{
		return $this->_count;
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  ArrayAccess
	 * ------------------------------------------------------------------------------------------ */
	
	public function offsetExists($index)
	{
		return array_key_exists($index, $this->_data);
	}
	
	public function offsetGet($index)
	{
		return $this->item($index);
	}
	
	public function offsetSet($index, $model)
	{
	}
	
	public function offsetUnset($index)
	{
	}
}