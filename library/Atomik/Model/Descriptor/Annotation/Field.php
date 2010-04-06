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

/** Atomik_Db_Type */
require_once 'Atomik/Db/Type.php';

/**
 * @Target("property")
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Descriptor_Annotation_Field extends Atomik_Model_Descriptor_Annotation
{
    public $type = 'string';
    
    public $length;
    
    public function apply(Atomik_Model_Descriptor $descriptor, $target)
    {
        $name = $target->getName();
        $type = Atomik_Db_Type::factory($this->type);
        
        if ($this->length !== null) {
            $type->setLength($this->length);
        }
        
        $field = new Atomik_Model_Field($name, $type);
        
        if ($target->getDeclaringClass()->getName() != $descriptor->getName() &&
            $descriptor->hasParent()) {
                $field->setInherited(true);
        }
        
        $descriptor->mapProperty($field);
    }
}