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

/**
 * @package Atomik
 * @subpackage Db
 */
abstract class Atomik_Db_Type_Abstract
{
    /** @var int */
    protected $_length;
    
    /**
     * @param int $length
     */
    public function __construct($length = null)
    {
        if ($length !== null) {
            $this->_length = $length;
        }
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        $className = get_class($this);
        return strtolower(substr($className, strrpos($className, '_') + 1));
    }
    
    /**
     * @param int $length
     */
    public function setLength($length)
    {
        $this->_length = $length;
    }
    
    /**
     * @return int
     */
    public function getLength()
    {
        return $this->_length;
    }
    
	/**
	 * Returns an array where the first item is the sql type name and the second the length
	 * 
	 * @return array
	 */
	abstract public function getSqlType();
	
	/**
	 * Filters the data from the database to php
	 * 
	 * @param mixed $output
	 * @return mixed
	 */
	public function filterInput($input)
	{
		return $input;
	}
	
	/**
	 * Filters the data from php to the database
	 * 
	 * @param mixed $input
	 * @return mixed
	 */
	public function filterOutput($output) 
	{
		return $output;
	}
}