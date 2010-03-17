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

/** Atomik_Db_Query */
require_once 'Atomik/Db/Query.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Query extends Atomik_Db_Query
{
	protected $_descriptor;
	
	public static function getAvailableFilters(Atomik_Model_Descriptor $descriptor)
	{
		$filters = array();
		foreach ($descriptor->getFields() as $field) {
			$fieldClass = get_class($field);
			$fieldType = substr($fieldClass, strrpos($fieldClass, '_') + 1);
			if ($filter = Atomik_Model_Query_Filter_Factory::factory($fieldType, $descriptor, $field)) {
				$filters[$field->name] = $filter;
			}
		}
		return $filters;
	}
	
	/**
	 * Creates a new query
	 * 
	 * @return Atomik_Model_Query
	 */
	public static function create()
	{
		return new self();
	}
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->reset();
	}
	
	/**
	 * Returns the associated descriptor
	 * 
	 * @return Atomik_Model_Descriptor
	 */
	public function getDescriptor()
	{
		return $this->_descriptor;
	}
	
	/**
	 * Sets which model to query 
	 * 
	 * @param	string|Atomik_Model_Descriptor $model
	 * @return 	Atomik_Model_Query
	 */
	public function from($model)
	{
		$this->_descriptor = Atomik_Model_Descriptor_Factory::get($model);
		$this->setInstance($this->_descriptor->getManager()->getDbInstance());
		return parent::from($this->_descriptor->tableName);
	}
	
	public function filter($fieldName, $value = null)
	{
		if (is_array($fieldName)) {
			foreach ($fieldName as $key => $value) {
				$this->filter($key, $value);
			}
			return $this;
		}
		
		if (empty($value)) {
			return $this;
		}
		
		$field = $this->_descriptor->getField($fieldName);
		$fieldClass = get_class($field);
		$fieldType = substr($fieldClass, strrpos($fieldClass, '_') + 1);
		if ($filter = Atomik_Model_Query_Filter_Factory::factory($fieldType, $this->_descriptor, $field)) {
			$filter->setValue($value);
			$condition = $filter->getQueryCondition();
			if (!empty($condition)) {
				$this->where($condition);
			}
		}
		return $this;
	}
}