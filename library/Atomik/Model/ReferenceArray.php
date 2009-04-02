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
class Atomik_Model_ReferenceArray implements Iterator, ArrayAccess, Countable
{
	/**
	 * @var Atomik_Model
	 */
	public $parent;
	
	/**
	 * @var Atomik_Model_Builder_Reference
	 */
	public $reference;
	
	/**
	 * @var array
	 */
	protected $_models = array();
	
	/**
	 * Constructor
	 *
	 * @param Atomik_Model $parent
	 * @param array $reference
	 * @param array $models
	 */
	public function __construct(Atomik_Model $parent, Atomik_Model_Builder_Reference $reference, $models = array())
	{
		$this->parent = $parent;
		$this->reference = $reference;
		$this->clear($models);
	}
	
	/**
	 * Resets all models from this reference
	 *
	 * @param array $models OPTIONAL An array of models to repopulate the reference
	 */
	public function clear($models = array())
	{
		// unsets old references
		foreach ($this->_models as $model) {
			$model->{$this->reference->targetField} = null;
			$model->save();
		}
		
		$this->_models = array();
		for ($i = 0, $c = count($models); $i < $c; $i++) {
			$this->offsetSet($i, $models[$i]);
		}
	}
	
	/**
	 * Saves all models contained is this reference
	 */
	public function save()
	{
		foreach ($this->_models as $model) {
			$model->save();
		}
	}
	
	/**
	 * Deletes all models contained is this relation
	 */
	public function delete()
	{
		foreach ($this->_models as $model) {
			$model->delete();
		}
		$this->_models = array();
	}
	
	/**
	 * Checks if the specified model is part of this reference
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function contains(Atomik_Model $model)
	{
		foreach ($this->_models as $modelComp) {
			if ($modelComp == $model) {
				return true;
			}
		}
		return false;
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
		// sets the foreign key on the foreign model
		$model->{$this->reference->targetField} = $this->parent->{$this->reference->sourceField};
		$this->_models[$index] = $model;
	}
	
	public function offsetUnset($index)
	{
		// unsets the foreign key on the foreign model
		$this->_models[$index]->{$this->reference->targetField} = null;
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