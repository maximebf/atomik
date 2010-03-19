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

/** Atomik_Model_Validator */
require_once 'Atomik/Model/Validator.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Validator_Callback implements Atomik_Model_Validator
{
    /** @var callback */
    protected $_callback;
    
    /**
     * @param callback $callback
     */
    public function __construct($callback)
    {
        $this->_callback = $callback;
    }
    
    /**
     * @param callback $callback
     */
    public function setCallback($callback)
    {
        $this->_callback = $callback;
    }
    
    /**
     * @return callback
     */
    public function getCallback()
    {
        return $this->_callback;
    }
    
    /**
     * @see Atomik_Model_Validator::isValid()
     */
    public function isValid($value)
    {
        return call_user_func($this->_callback, $value);
    }
}