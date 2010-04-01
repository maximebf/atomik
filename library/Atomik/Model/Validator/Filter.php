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

/** Atomik_Model_Validator_Abstract */
require_once 'Atomik/Model/Validator/Abstract.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Validator_Filter implements Atomik_Model_Validator_Abstract
{
    /** @var int */
    protected $_filter;
    
    /** @var array */
    protected $_options;
    
    /**
     * @param string|int $filter
     * @param array $options
     */
    public function __construct($filter, $options)
    {
        $this->setFilter($filter);
        $this->setOptions($options);
    }
    
    /**
     * @param string|int $filter
     */
    public function setFilter($filter)
    {
        if (is_string($filter)) {
            $filter = filter_id($filter);
        }
        $this->_filter = $filter;
    }
    
    /**
     * @return int
     */
    public function getFilter()
    {
        return $this->_filter;
    }
    
    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }
    
    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }
    
    /**
     * @see Atomik_Model_Validator::isValid()
     */
    public function isValid($value)
    {
        return filter_var($value, $this->_filter, $this->_options) !== false;
    }
}