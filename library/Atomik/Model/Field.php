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

/** Atomik_Model_Descriptor_Property */
require_once 'Atomik/Model/Descriptor/Property.php';

/** Atomik_Db_Type */
require_once 'Atomik/Db/Type.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Field extends Atomik_Model_Descriptor_Property
{
	/** @var Atomik_Db_Type_Abstract */
	protected $_type;
    
    /**
     * @param string $name
     * @param string|Atomik_Db_Type_Abstract $type
     * @return Atomik_Model_Field
     */
    public static function factory($name, $type, $length = null)
    {
        if (is_string($type)) {
            $type = Atomik_Db_Type::factory($type, $length);
        }
        return new Atomik_Model_Field($name, $type);
    }
    
    /**
     * @param string $name
     * @param Atomik_Db_Type_Abstract $type
     */
    public function __construct($name, Atomik_Db_Type_Abstract $type)
    {
        $this->setName($name);
        $this->setType($type);
    }
    
    /**
     * @param Atomik_Db_Type_Abstract $type
     */
    public function setType(Atomik_Db_Type_Abstract $type)
    {
        $this->_type = $type;
    }
    
    /**
     * @return Atomik_Db_Type_Abstract
     */
    public function getType()
    {
        return $this->_type;
    }
}