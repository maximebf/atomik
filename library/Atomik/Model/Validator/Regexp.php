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
class Atomik_Model_Validator_Regexp implements Atomik_Model_Validator_Abstract
{
    /** @var string */
    protected $_regexp;
    
    /**
     * @param string $regexp
     */
    public function __construct($regexp)
    {
        $this->_regexp = $regexp;
    }
    
    /**
     * @param string $regexp
     */
    public function setRegexp($regexp)
    {
        $this->_regexp = $regexp;
    }
    
    /**
     * @return string
     */
    public function getRegexp()
    {
        return $this->_regexp;
    }
    
    /**
     * @see Atomik_Model_Validator::isValid()
     */
    public function isValid($value)
    {
        return preg_match($this->_regexp, $value);
    }
}