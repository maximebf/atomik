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
 * @subpackage Db
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Db_Query_Generator_Interface */
require_once 'Atomik/Db/Query/Generator/Interface.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Query_Generator implements Atomik_Db_Query_Generator_Interface
{
	/**
	 * @var Atomik_Db_Adapter_Interface
	 */
	protected $_adapter;
	
	/**
	 * @var Atomik_Db_Query
	 */
	protected $_query;
	
	/**
	 * @var array
	 */
	protected $_info;
	
	public function __construct(Atomik_Db_Adapter_Interface $adapter)
	{
		$this->_adapter = $adapter;
	}
	
	/**
	 * Returns the query as an SQL string
	 * 
	 * @return string
	 */
	public function generate(Atomik_Db_Query $query)
	{
		$this->_query = $query;
		$this->_info = $query->getInfo();
		
		$sql = '';
		
		switch($this->_info['statement']) {
			case 'SELECT':
				$sql = $this->_buildSelectStatement();
				break;
			case 'INSERT':
				$sql = $this->_buildInsertStatement();
				break;
			case 'UPDATE':
				$sql = $this->_buildUpdateStatement();
				break;
			case 'DELETE':
				$sql = $this->_buildDeleteStatement();
				break;
		}
		
		return trim($sql);
	}
	
	/**
	 * Builds a SELECT statement
	 * 
	 * @return string
	 */
	protected function _buildSelectStatement()
	{
		if (count($fields = $this->_info['fields']) == 0) {
			$fields = array('*');
		}
		
		return 	'SELECT '
				. implode(', ', $fields)
				. $this->_buildFromPart()
				. $this->_buildJoinPart()
				. $this->_buildWherePart()
				. $this->_buildGroupByPart()
				. $this->_buildOrderByPart()
				. $this->_buildLimitPart();
	}
	
	/**
	 * Builds an INSERT statement
	 * 
	 * @return string
	 */
	protected function _buildInsertStatement()
	{
		$data = $this->_info['data'];
		
		if (!is_array($data)) {
			return sprintf('INSERT INTO %s %s',
				$this->_adapter->quoteIdentifier($this->_info['table']),
				(string) $data
			);
			
		} else {
			return sprintf('INSERT INTO %s (%s) VALUES (%s)',
				$this->_adapter->quoteIdentifier($this->_info['table']),
				implode(', ', array_map(array($this->_adapter, 'quoteIdentifier'), array_keys($data))),
				implode(', ', array_values($data))
			);
		}
	}
	
	/**
	 * Builds an UPDATE statement
	 * 
	 * @return string
	 */
	protected function _buildUpdateStatement()
	{
		$sets = array();
		foreach ($this->_info['data'] as $field => $value) {
			$sets[] = $this->_adapter->quoteIdentifier($field) . ' = ' .$value;
		}
		
		return sprintf('UPDATE %s SET %s',
			$this->_adapter->quoteIdentifier($this->_info['table']),
			implode(', ', $sets) .
			$this->_buildWherePart() .
			$this->_buildOrderByPart() .
			$this->_buildLimitPart()
		);
	}
	
	/**
	 * Builds a DELETE statement
	 * 
	 * @return string
	 */
	protected function _buildDeleteStatement()
	{
		return 	'DELETE'
				. $this->_buildFromPart()
				. $this->_buildWherePart()
				. $this->_buildOrderByPart()
				. $this->_buildLimitPart();
	}
	
	/**
	 * Builds the FROM part
	 * 
	 * @return string
	 */
	protected function _buildFromPart()
	{
		$sql = '';
		
		if (count($this->_info['from'])) {
			$tables = array();
			foreach ($this->_info['from'] as $fromInfo) {
				$fromSql = $this->_adapter->quoteIdentifier($fromInfo['table']);
				if (!empty($fromInfo['alias'])) {
					$fromSql .= ' AS ' . $fromInfo['alias'];
				}
				$tables[] = $fromSql;
			}
			$sql = ' FROM ' . implode(', ', $tables);
		}
		
		return $sql;
	}
	
	/**
	 * Builds the JOIN part
	 * 
	 * @return string
	 */
	protected function _buildJoinPart()
	{
		$sql = '';
		
		if (count($this->_info['join'])) {
			foreach ($this->_info['join'] as $joinInfo) {
				$sql .= ' ' . trim(strtoupper($joinInfo['type'])) 
					  . ' JOIN ' 
					  . $this->_adapter->quoteIdentifier($joinInfo['table'])
					  . (!empty($joinInfo['alias']) ? ' AS ' . $joinInfo['alias'] : '')
					  . ' ON '
					  . $joinInfo['on'];
			}
		}
		
		return $sql;
	}
	
	/**
	 * Builds the WHERE part
	 * 
	 * @return string
	 */
	protected function _buildWherePart()
	{
		$sql = '';
		
		$where = $this->_query->getConditionString();
		if (!empty($where)) {
			$sql = ' WHERE ' . $where;
		}
		
		return $sql;
	}
	
	/**
	 * Builds the GROUP BY part
	 * 
	 * @return string
	 */
	protected function _buildGroupByPart()
	{
		$sql = '';
		
		if (count($this->_info['groupBy'])) {
			$sql = ' GROUP BY ' . implode(', ', $this->_info['groupBy']);
			if (count($this->_info['having'])) {
				$sql .= ' HAVING ' . $this->_query->_concatConditions($this->_info['having']);
			}
		}
		
		return $sql;
	}
	
	/**
	 * Builds the ORDER BY part
	 * 
	 * @return string
	 */
	protected function _buildOrderByPart()
	{
		$sql = '';
		
		if (is_string($this->_info['orderBy'])) {
			return ' ORDER BY ' . $this->_info['orderBy'];
		}
		
		if (count($this->_info['orderBy'])) {
			$fields = array();
			foreach ($this->_info['orderBy'] as $field => $direction) {
				$fieldSql = $field;
				if (!empty($direction)) {
					$fieldSql .= ' ' . $direction;
				}
				$fields[] = $fieldSql;
			}
			$sql = ' ORDER BY ' . implode(', ', $fields);
		}
		
		return $sql;
	}
	
	/**
	 * Builds the LIMIT part
	 * 
	 * @return string
	 */
	protected function _buildLimitPart()
	{
		$sql = '';
		
		if (!empty($this->_info['limit'])) {
			$sql = ' LIMIT ' . $this->_info['limit']['offset'] . ', ' . $this->_info['limit']['length'];
		}
		
		return $sql;
	}
}