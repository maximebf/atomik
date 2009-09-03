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

/** Atomik_Model_Behaviour_Abstract */
require_once 'Atomik/Model/Behaviour/Abstract.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Behaviour_Cacheable extends  Atomik_Model_Behaviour_Abstract
{
	/**
	 * @var Memcache
	 */
	protected $_memcache;
	
	/**
	 * @var Memcache
	 */
	private static $_defaultMemcache;
	
	public static function setDefaultMemcache(Memcache $memcache)
	{
		self::$_defaultMemcache = $memcache;
	}
	
	public static function getDefaultMemcache()
	{
		return self::$_defaultMemcache;
	}
	
	public function setMemcache(Memcache $memcache)
	{
		$this->_memcache = $memcache;
	}
	
	public function getMemcache()
	{
		return $this->_memcache;
	}
	
	public function beforeQuery(Atomik_Model_Builder $builder, Atomik_Db_Query $query)
	{
		// only select the primary key
		$query->clearSelect()->select($builder->tableName . '.' . $builder->getPrimaryKeyField()->name);
	}
	
	public function afterQuery(Atomik_Model_Builder $builder, Atomik_Model_Modelset $modelSet)
	{
		$modelName = $builder->name;
		$primaryKeyName = $builder->getPrimaryKeyField()->name;
		$manager = $builder->getManager();
		$db = $manager->getDbInstance();
		$rows = array();
		
		$dataQuery = $db->q()->select()
				->from($builder->tableName)
				->where(array($primaryKeyName => null));
		
		foreach ($modelSet as $row) {
			$primaryKey = $row[$primaryKeyName];
			$key = $modelName . ':' . $primaryKey;
			
			if (($cached = $this->_memcache->get($key)) !== false) {
				// cache hit
				$rows[] = $cached;
				continue;
			}
			
			$data = $dataQuery->setParams(array($primaryKey))->execute()->fetch();
			$this->_memcache->set($key, $data);
			$rows[] = $data;
		}
		
		$modelSet->setData($rows);
	}
	
	public function afterSave(Atomik_Model_Builder $builder, Atomik_Model $model)
	{
		$key = $builder->name . ':' . $model->getPrimaryKey();
		$data = $model->toArray();
		
		if ($this->_memcache->replace($key, $data) === false) {
			$this->_memcache->set($key, $data);
		}
	}
	
	public function afterDelete(Atomik_Model_Builder $builder, Atomik_Model $model)
	{
		$key = $builder->name . ':' . $model->getPrimaryKey();
		$this->_memcache->delete($key);
	}
}