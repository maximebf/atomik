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

/** Atomik_Model */
require_once 'Atomik/Model.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_AssocCollection implements Iterator, ArrayAccess, Countable
{
	/** @var Atomik_Model */
	protected $_owner;
	
	/** @var Atomik_Model_Association */
	protected $_association;
	
	/** @var array */
	protected $_models = array();
	
	/**
	 * @param Atomik_Model $owner
	 * @param Atomik_Model_Association $assoc
	 * @param Atomik_Model_Collection $collection
	 */
	public function __construct(Atomik_Model $owner, Atomik_Model_Association $assoc, Atomik_Model_Collection $collection = null)
	{
		$this->_owner = $owner;
		$this->_association = $assoc;
		if ($collection !== null) {
		    $this->_models = $collection->getAll();
		}
	}
	
	/**
	 * @param Atomik_Model $model
	 */
	public function add(Atomik_Model $model)
	{
	    $this->_models[] = $model;
	}
	
	/**
	 * @param int $index
	 */
	public function get($index)
	{
	    return $this->_models[$index];
	}
	
	/**
	 * @param Atomik_Model $model
	 */
	public function remove(Atomik_Model $model)
	{
	    unset($this->_models[array_search($model, $this->_models)]);
	}
	
	/**
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function contains(Atomik_Model $model)
	{
		return array_search($model, $this->_models) !== false;
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  Countable
	 * ------------------------------------------------------------------------------------------ */
	
	public function count()
	{
		return count($this->_models);
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  ArrayAccess
	 * ------------------------------------------------------------------------------------------ */
	
	public function offsetExists($index)
	{
		return array_key_exists($index, $this->_models);
	}
	
	public function offsetGet($index)
	{
		return $this->_models[$index];
	}
	
	public function offsetSet($index, $model)
	{
		$this->_models[$index] = $model;
	}
	
	public function offsetUnset($index)
	{
		unset($this->_models[$index]);
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  Iterator
	 * ------------------------------------------------------------------------------------------ */
	
	public function current()
	{
		return current($this->_models);
	}
	
	public function key()
	{
		return key($this->_models);
	}
	
	public function next()
	{
		return next($this->_models);
	}
	
	public function rewind()
	{
		return reset($this->_models);
	}
	
	public function valid()
	{
		return $this->current() !== false;
	}
}