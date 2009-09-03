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
class Atomik_Model_Locator
{
	/**
	 * Returns a new query
	 * 
	 * @return Atomik_Model_Query
	 */
	public static function createQuery()
	{
		return new Atomik_Model_Query();
	}
	
	/**
	 * Query the adapter
	 *
	 * @param 	string|Atomik_Model_Builder $builder
	 * @param 	Atomik_Db_Query				$query
	 * @return 	Atomik_Model_Modelset
	 */
	public static function query(Atomik_Db_Query $query, $builder = null)
	{
		if ($builder !== null) {
			$builder = Atomik_Model_Builder_Factory::get($builder);
			$manager = $builder->getManager();
		} else {
			$manager = Atomik_Model_Manager::getDefault();
		}
		
		return $manager->query($query);
	}
	
	/**
	 * Finds many models
	 *
	 * @param 	string|Atomik_Model_Builder 	$builder
	 * @param 	array 							$where
	 * @param 	string 							$orderBy
	 * @param 	string|array					$limit
	 * @return 	Atomik_Model_Modelset
	 */
	public static function findAll($builder, $where = null, $orderBy = null, $limit = null)
	{
		return self::query(self::buildQuery($builder, $where, $orderBy, $limit), $builder);
	}
	
	/**
	 * Finds one model
	 *
	 * @param 	string|Atomik_Model_Builder 	$builder
	 * @param 	array 							$where
	 * @param 	string 							$orderBy
	 * @param 	string|array					$limit
	 * @return 	Atomik_Model
	 */
	public static function findOne($builder, $where, $orderBy = null, $offset = 0)
	{
		$query = self::buildQuery($builder, $where, $orderBy);
		$query->limit($offset, 1);
		
		$modelSet = self::query($query, $builder);
		if (count($modelSet) == 0) {
			return null;
		}
		return $modelSet[0];
	}
	
	/**
	 * Finds one model
	 *
	 * @param 	string|Atomik_Model_Builder 	$builder
	 * @param 	string 							$primaryKey
	 * @return 	Atomik_Model
	 */
	public static function find($builder, $primaryKey)
	{
		$builder = Atomik_Model_Builder_Factory::get($builder);
		$where = array($builder->getPrimaryKeyField()->name => $primaryKey);
		return self::findOne($builder, $where);
	}
	
	/**
	 * Returns the number of rows the query will return
	 *
	 * @param 	string|Atomik_Model_Builder|Atomik_Db_Query 	$builder
	 * @param 	array 											$where
	 * @param 	string 											$orderBy
	 * @param 	string|array									$limit
	 * @return 	Atomik_Model_Modelset
	 */
	public static function count($builder, $where = null, $orderBy = null, $limit = null)
	{
		if ($builder instanceof Atomik_Db_Query) {
			$query = clone $builder;
			$query->count();
			$builder = Atomik_Model_Manager::getBuilderFromQuery($query);
			return $builder->getManager()->getDbInstance()->count($query);
		}
		
		$query = self::buildQuery($builder, $where, $orderBy, $limit);
		return self::query($query->count(), $builder);
	}
	
	/**
	 * Builds a query object from the parameters
	 *
	 * @param 	string|Atomik_Model_Builder $builder
	 * @param 	array 						$where
	 * @param 	string 						$orderBy
	 * @param 	string|array				$limit
	 * @return 	Atomik_Db_Query
	 */
	public static function buildQuery($builder, $where = null, $orderBy = null, $limit = null)
	{
		$query = Atomik_Model_Query::create($builder);
		$query->select()->from($builder);
		
		if ($where !== null) {
			$query->where($where);
		}
		
		if ($orderBy !== null) {
			$query->orderBy($orderBy);
		}
		
		if ($limit !== null) {
			$query->limit($limit);
		}
		
		return $query;
	}
}