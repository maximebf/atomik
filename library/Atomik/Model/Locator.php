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

/** Atomik_Model_Query */
require_once 'Atomik/Model/Query.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Locator
{
	/**
	 * Query the adapter
	 *
	 * @param 	string|Atomik_Model_Descriptor $descriptor
	 * @param 	Atomik_Db_Query				$query
	 * @return 	Atomik_Model_Modelset
	 */
	public static function query(Atomik_Db_Query $query)
	{
		$session = Atomik_Model_Session::getDefault();
		return $session->query($query);
	}
	
	/**
	 * Finds many models
	 *
	 * @param 	string|Atomik_Model_Descriptor 	$descriptor
	 * @param 	array 							$where
	 * @param 	string 							$orderBy
	 * @param 	string|array					$limit
	 * @return 	Atomik_Model_Modelset
	 */
	public static function findAll($descriptor, $where = null, $orderBy = null, $limit = null)
	{
		return self::query(self::buildQuery($descriptor, $where, $orderBy, $limit), $descriptor);
	}
	
	/**
	 * Finds one model
	 *
	 * @param 	string|Atomik_Model_Descriptor 	$descriptor
	 * @param 	array 							$where
	 * @param 	string 							$orderBy
	 * @param 	string|array					$limit
	 * @return 	Atomik_Model
	 */
	public static function findOne($descriptor, $where, $orderBy = null, $offset = 0)
	{
		$query = self::buildQuery($descriptor, $where, $orderBy);
		$query->limit($offset, 1);
		
		$modelSet = self::query($query, $descriptor);
		if (count($modelSet) == 0) {
			return null;
		}
		return $modelSet[0];
	}
	
	/**
	 * Finds one model
	 *
	 * @param 	string|Atomik_Model_Descriptor 	$descriptor
	 * @param 	string|array					$where
	 * @return 	Atomik_Model
	 */
	public static function find($descriptor, $where)
	{
		$descriptor = Atomik_Model_Descriptor::factory($descriptor);
		
		if (!is_array($where)) {
		    $where = array($descriptor->getPrimaryKeyField()->getColumnName() => $where);
		}
		
		return self::findOne($descriptor, $where);
	}
	
	/**
	 * Returns the number of rows the query will return
	 *
	 * @param 	string|Atomik_Model_Descriptor|Atomik_Db_Query 	$descriptor
	 * @param 	array 											$where
	 * @param 	string 											$orderBy
	 * @param 	string|array									$limit
	 * @return 	Atomik_Model_Modelset
	 */
	public static function count($descriptor, $where = null, $orderBy = null, $limit = null)
	{
		if ($descriptor instanceof Atomik_Db_Query) {
			$query = clone $descriptor;
			$query->count();
			$descriptor = Atomik_Model_Session::getDescriptorFromQuery($query);
			return $descriptor->getManager()->getDbInstance()->count($query);
		}
		
		$query = self::buildQuery($descriptor, $where, $orderBy, $limit);
		return self::query($query->count(), $descriptor);
	}
	
	/**
	 * Builds a query object from the parameters
	 *
	 * @param 	string|Atomik_Model_Descriptor $descriptor
	 * @param 	array 						$where
	 * @param 	string 						$orderBy
	 * @param 	string|array				$limit
	 * @return 	Atomik_Db_Query
	 */
	public static function buildQuery($descriptor, $where = null, $orderBy = null, $limit = null)
	{
		$query = Atomik_Model_Query::from($descriptor);
		
		if ($where !== null) {
		    foreach ($where as $key => $value) {
		        
		    }
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