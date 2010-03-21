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

/** Atomik_Db_Type_Abstract */
require_once 'Atomik/Db/Type/Abstract.php';

/** Atomik_Db_Type_String */
require_once 'Atomik/Db/Type/String.php';

/** Atomik_Db_Type_Text */
require_once 'Atomik/Db/Type/Text.php';

/** Atomik_Db_Type_Bool */
require_once 'Atomik/Db/Type/Bool.php';

/** Atomik_Db_Type_Datetime */
require_once 'Atomik/Db/Type/Datetime.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Type extends Atomik_Db_Type_Abstract
{
    /** @var string */
    protected $_sqlType;
    
    /** @var array */
    private static $_types = array(
        'bool'     => 'Atomik_Db_Type_Bool',
        'string'   => 'Atomik_Db_Type_String',
        'text'     => 'Atomik_Db_Type_Text',
        'datetime' => 'Atomik_Db_Type_Datetime'
    );
    
    /**
     * @param string $type
     * @return Atomik_Db_Type_Abstract
     */
    public static function factory($type, $length = null)
    {
        $type = strtolower($type);
        
        if (!isset(self::$_types[$type])) {
            return new Atomik_Db_Type($type, $length);
        }
        
        $className = self::$_types[$type];
        return new $className($length);
    }
    
    /**
     * @param string $name
     * @param string $className
     */
    public static function addType($name, $className)
    {
        self::$_types[strtolower($name)] = $className;
    }
    
    /**
     * @param string $sqlType
     * @param int $length
     */
    public function __construct($sqlType, $length = null)
    {
        $this->_sqlType = $sqlType;
        $this->_length = $length;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return strtolower($this->_sqlType);
    }
    
	/**
	 * @see Atomik_Db_Type_Abstract::getSqlType()
	 */
	public function getSqlType()
	{
	    if ($this->_length !== null) {
	        return $this->_sqlType . '(' . $this->_length . ')';
	    }
	    return $this->_sqlType;
	}
}