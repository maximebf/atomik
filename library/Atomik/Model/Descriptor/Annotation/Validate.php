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

/** Atomik_Model_Descriptor_Annotation */
require_once 'Atomik/Model/Descriptor/Annotation.php';

/**
 * @Target("property")
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Descriptor_Annotation_Validate extends Atomik_Model_Descriptor_Annotation
{
    public $filter;
    
    public $options;
    
    public $regexp;
    
    public $callback;
    
    public $class;
    
    public $args = array();
    
    public $required;
    
    public function apply(Atomik_Model_Descriptor $descriptor, $target)
    {
        $prop = $descriptor->getMappedProperty($target->getName());
        
        if ($this->required !== null) {
            $prop->setRequired($this->required);
        }
        
        if (!empty($this->filter)) {
            require_once 'Atomik/Model/Validator/Filter.php';
            $validator = new Atomik_Model_Validator_Filter($this->filter, $this->options);
        } else if (!empty($this->regexp)) {
            require_once 'Atomik/Model/Validator/Regexp.php';
            $validator = new Atomik_Model_Validator_Regexp($this->regexp);
        } else if (!empty($this->callback)) {
            require_once 'Atomik/Model/Validator/Callback.php';
            $validator = new Atomik_Model_Validator_Callback($this->callback);
        } else if (!empty($this->class)) {
            if (!class_exists($this->class)) {
                throw new Atomik_Model_Exception("Validator class '{$this->class}' not found in '{$descriptor->getName()}'");
            }
            $class = new ReflectionClass($this->class);
            $validator = $class->newInstanceArgs($this->args);
        } else if ($this->required !== null) {
            return;
        } else {
            throw new Atomik_Exception("Invalid @Validate in '{$descriptor->getName()}'");
        }
        
        $prop->addValidator($validator);
    }
}