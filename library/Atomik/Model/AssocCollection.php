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

/** Atomik_Model_Collection */
require_once 'Atomik/Model/Collection.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_AssocCollection extends Atomik_Model_Collection
{
	/** @var Atomik_Model */
	protected $_owner;
	
	/** @var Atomik_Model_Association */
	protected $_association;
	
	/** @var array */
	protected $_models = array();
	
	/** @var array */
	protected $_changeset;
	
	/**
	 * @param Atomik_Model $owner
	 * @param Atomik_Model_Association $assoc
	 * @param Atomik_Model_Collection $collection
	 */
	public function __construct(Atomik_Model $owner, Atomik_Model_Association $assoc, $data = array())
	{
	    $this->resetChangeset();
		$this->_owner = $owner;
		$this->_association = $assoc;
		parent::__construct($assoc->getTarget(), $data);
	}
	
	/**
	 * Returns the owner of this collection
	 * 
	 * @return Atomik_Model
	 */
	public function getOwner()
	{
	    return $this->_owner;
	}
	
	/**
	 * Returns the association used for this collection
	 * 
	 * @return Atomik_Model_Association
	 */
	public function getAssociation()
	{
	    return $this->_association;
	}
	
	/**
	 * @param Atomik_Model $model
	 */
	public function add(Atomik_Model $model)
	{
	    $this->_models[] = $model;
        $this->_addToChangeset('added', $model);
	    $this->_count++;
	}
	
	/**
	 * @param Atomik_Model $model
	 */
	public function remove(Atomik_Model $model)
	{
	    $this->getAll();
	    for ($i = 0, $c = count($this->_models); $i < $c; $i++) {
	        if ($this->_models[$i] == $model) {
	            unset($this->_models[$i]);
	            if (isset($this->_data[$i])) {
	                unset($this->_data[$i]);
	            }
	            $this->_addToChangeset('removed', $model);
	        }
	    }
	    $this->_count--;
	}
	
	public function removeAll()
	{
	    $this->getAll();
	    foreach ($this->_models as $model) {
	        $this->_addToChangeset('removed', $model);
	    }
	    
	    $this->setData(array());
	}
	
	/**
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function contains(Atomik_Model $model)
	{
	    $this->getAll();
		return in_array($model, $this->_models);
	}
	
	/**
	 * Adds a model to the changeset, removing it from the other type
	 * of operation if necessary
	 * 
	 * @param string $type
	 * @param Atomik_Model $model
	 */
	protected function _addToChangeset($type, Atomik_Model $model)
	{
	    $otherType = $type == 'removed' ? 'added' : 'removed';
	    for ($i = 0, $c = count($this->_changeset[$otherType]); $i < $c; $i++) {
	        if ($this->_changeset[$otherType][$i] == $model) {
	            unset($this->_changeset[$otherType][$i]);
	        }
	    }
	    $this->_changeset[$type][] = $model;
	}
	
	/**
	 * Returns a list of added and removed models
	 * 
	 * @return array
	 */
	public function getChangeset()
	{
	    return $this->_changeset;
	}
	
	public function resetChangeset()
	{
	    $this->_changeset = array('added' => array(), 'removed' => array());
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  ArrayAccess
	 * ------------------------------------------------------------------------------------------ */
	
	public function offsetSet($index, $model)
	{
		$this->_models[$index] = $model;
	}
	
	public function offsetUnset($index)
	{
		unset($this->_models[$index]);
	}
}