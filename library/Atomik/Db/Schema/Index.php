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

/** Atomik_Db_Schema_Column */
require_once 'Atomik/Db/Schema/Column.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Schema_Index
{
    /** @var string */
	protected $_name;
	
    /** @var Atomik_Db_Schema_Column */
	protected $_column;
	
	/**
	 * @param string $name
	 * @param Atomik_Db_Schema_Column $column
	 */
	public function __construct($name, Atomik_Db_Schema_Column $column)
	{
		$this->_name = $name;
		$this->_column = $column;
	}
	
	/**
	 * Sets the column's name
	 * 
	 * @param string $name
	 */
	public function setName($name)
	{
	    $this->_name = $name;
	}
	
	/**
	 * Returns the column's name
	 * 
	 * @return string
	 */
	public function getName()
	{
	    return $this->_name;
	}
	
	/**
	 * Sets the column on which this index applies
	 * 
	 * @param Atomik_Db_Schema_Column $column
	 */
	public function setColumn(Atomik_Db_Schema_Column $column)
	{
	    $this->_column = $column;
	}
	
	/**
	 * @return Atomik_Db_Schema_Column
	 */
	public function getColumn()
	{
	    return $this->_column;
	}
}