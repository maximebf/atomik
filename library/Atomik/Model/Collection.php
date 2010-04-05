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

/** Atomik_Model_Descriptor */
require_once 'Atomik/Model/Descriptor.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Collection implements Iterator, ArrayAccess, Countable
{
	/** @var int */
	protected $_pointer = 0;
	
	/** @var int */
	protected $_count;
	
	/** @var Atomik_Model_Descriptor */
	protected $_descriptor;
	
	/** @var array */
	protected $_data = array();
	
	/** @var array of Atomik_Model */
	protected $_models = array();
	
	/**
	 * @param Atomik_Model_Session $session
	 * @param Atomik_Model_Descriptor $descriptor
	 * @param array $data
	 */
	public function __construct(Atomik_Model_Descriptor $descriptor, $data = array())
	{
	    $this->_descriptor = $descriptor;
		$this->setData($data);
	}
	
	/**
	 * @param Atomik_Model_Descriptor $descriptor
	 */
	public function setDescriptor(Atomik_Model_Descriptor $descriptor)
	{
	    $this->_descriptor = $descriptor;
		$this->_pointer = 0;
		$this->_models = array();
	}
	
	/**
	 * @return Atomik_Model_Descriptor
	 */
	public function getDescriptor()
	{
	    return $this->_descriptor;
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
	 * Returns the first model in the collection
	 * 
	 * @return Atomik_Model
	 */
	public function getFirst()
	{
	    $model = $this->item(0);
	    return $model === false ? null : $model;
	}
	
	/**
	 * Returns all the models as an array
	 * 
	 * @return array of Atomik_Model
	 */
	public function getAll()
	{
        $this->current();
		while ($this->next()) {
			$this->current();
		}
		return $this->_models;
	}
	
	/**
	 * Returns the model at the specified index
	 * 
	 * @param int $index
	 * @return Atomik_Model
	 */
	public function item($index)
	{
		if ($index >= $this->_count) {
			return false;
		}
		
		if (!isset($this->_models[$index])) {
			$this->_models[$index] = $this->_descriptor->hydrate($this->_data[$index]);
		}
		
		return $this->_models[$index];
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
