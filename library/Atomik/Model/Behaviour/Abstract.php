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

/** Atomik_Model_Behaviour_Interface */
require_once 'Atomik/Model/Behaviour/Interface.php';

/**
 * @package Atomik
 * @subpackage Model
 */
abstract class Atomik_Model_Behaviour_Abstract implements Atomik_Model_Behaviour_Interface
{
	/**
	 * @var Atomik_Model_Builder
	 */
	protected $_builder;
	
	/**
	 * Sets the associated builder
	 * 
	 * @param Atomik_Model_Builder $builder
	 */
	public function setBuilder(Atomik_Model_Builder $builder)
	{
		$this->_builder = $builder;
		$this->init();
	}
	
	/**
	 * Returns the associated model builder
	 * 
	 * @return Atomik_Model_Builder
	 */
	public function getBuilder()
	{
		return $this->_builder;
	}
	
	public function init() {}
	
	public function beforeQuery(Atomik_Db_Query $query) {}
	
	public function afterQuery(Atomik_Model_Modelset $modelSet) {}
	
	public function beforeCreateInstance(&$data, $isNew) {}
	
	public function afterCreateInstance(Atomik_Model $model) {}
	
	public function beforeSave(Atomik_Model $model) {}
	
	public function failSave(Atomik_Model $model) {}
	
	public function afterSave(Atomik_Model $model) {}
	
	public function beforeDelete(Atomik_Model $model) {}
	
	public function failDelete(Atomik_Model $model) {}
	
	public function afterDelete(Atomik_Model $model) {}
}