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
	 * @param 	string|Atomik_Model_Builder $builder
	 * @param 	Atomik_Model_Query			$query
	 * @return 	Atomik_Model_Modelset
	 */
	public static function query($builder, Atomik_Model_Query $query)
	{
		$builder = Atomik_Model_Builder_Factory::get($builder);
		$query->from($builder);
		return $builder->getAdapter()->query($query);
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
		return self::query($builder, self::buildQuery($where, $orderBy, $limit));
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
	public static function find($builder, $where, $orderBy = null, $offset = 0)
	{
		$query = self::buildQuery($where, $orderBy);
		$query->limit(1, $offset);
		
		$modelSet = self::query($builder, $query);
		if (count($modelSet) == 0) {
			return null;
		}
		return $modelSet[0]; 
	}
	
	/**
	 * Builds a query object from the parameters
	 *
	 * @param 	array 				$where
	 * @param 	string 				$orderBy
	 * @param 	string|array		$limit
	 * @return 	Atomik_Model_Query
	 */
	public static function buildQuery($where = null, $orderBy = null, $limit = null)
	{
		$query = new Atomik_Model_Query();
		
		if ($where !== null && is_array($where)) {
			$query->where($where);
		}
		
		if ($orderBy !== null) {
			if (preg_match('/^(.+)\s+(ASC|DESC)$/', $orderBy, $matches)) {
				$query->orderBy($matches[1], $matches[2]);
			} else {
				$query->orderBy($orderBy);
			}
		}
		
		if ($limit !== null) {
			if (!is_array($limit)) {
				$limit = explode(',', $limit);
			}
			$query->limit($limit[0], count($limit) == 2 ? $limit[1] : 0);
		}
		
		return $query;
	}
}