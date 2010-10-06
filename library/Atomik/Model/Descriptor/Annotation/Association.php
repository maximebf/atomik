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
class Atomik_Model_Descriptor_Annotation_Association extends Atomik_Model_Descriptor_Annotation
{
    public $has_one;
    
    public $has_parent;
    
    public $has_many;
    
    public $has_many_to_many;
    
    public $via;
    
    public $sourceField;
    
    public $targetField;
    
    public $viaSourceField;
    
    public $viaTargetField;
    
    public function apply(Atomik_Model_Descriptor $descriptor, $property)
    {
        $name = $property->getName();
        
        if (!empty($this->has_parent)) {
            $target = $this->getTargetDescriptor($this->has_parent);
            require_once 'Atomik/Model/Association/ManyToOne.php';
            $assoc = new Atomik_Model_Association_ManyToOne($name, $descriptor, $target);
            
        } else if (!empty($this->has_one)) {
            $target = $this->getTargetDescriptor($this->has_one);
            require_once 'Atomik/Model/Association/OneToMany.php';
            $assoc = new Atomik_Model_Association_OneToMany($name, $descriptor, $target);
            
        } else if (!empty($this->has_many) && empty($this->via)) {
            $target = $this->getTargetDescriptor($this->has_many);
            require_once 'Atomik/Model/Association/OneToMany.php';
            $assoc = new Atomik_Model_Association_OneToMany($name, $descriptor, $target);
            
        } else if (!empty($this->has_many_to_many) || (!empty($this->has_many) && !empty($this->via))) {
            $targetName = empty($this->has_many) ? $this->has_many_to_many : $this->has_many;
            $target = $this->getTargetDescriptor($targetName);
            
            require_once 'Atomik/Model/Association/ManyToMany.php';
            $assoc = new Atomik_Model_Association_ManyToMany($name, $descriptor, $target);
            
            if (empty($this->via)) {
                $this->via = strtolower($descriptor->getName() . '_' . $target->getName());
            }
            
		    $assoc->setViaTable($this->via);
		    !empty($this->viaSourceField) && $assoc->setViaSourceField($this->viaSourceField);
		    !empty($this->viaTargetField) && $assoc->setViaTargetField($this->viaTargetField);
		    
        } else {
            throw new Atomik_Model_Descriptor_Exception("No target specified for association '$name' in '" 
                . $descriptor->getName() . "'");
        }
        
		!empty($this->sourceField) && $assoc->setSourceField($this->sourceField);
		!empty($this->targetField) && $assoc->setTargetField($this->targetField);
		
		if (!$descriptor->hasField($assoc->getSourceField())) {
		    $field = Atomik_Model_Field::factory($assoc->getSourceField(), 'int');
		    $descriptor->mapProperty($field);
		}
		if (!$target->hasField($assoc->getTargetField())) {
		    $field = Atomik_Model_Field::factory($assoc->getTargetField(), 'int');
		    $target->mapProperty($field);
		}
		
        if ($property->getDeclaringClass()->getName() != $descriptor->getName() &&
            $descriptor->hasParent()) {
                if ($descriptor->getIdentifierField()->getName() != $assoc->getSourceField()) {
                    $descriptor->getField($assoc->getSourceField())->setInherited(true);
                }
                $assoc->setInherited(true);
        }
		
		$descriptor->mapProperty($assoc);
    }
    
    public function getTargetDescriptor($target)
    {
        $className = $target;
        if (strpos($className, '\\') === false) {
            $className = ltrim(Atomik_Model_Descriptor::getDefaultNamespace() . '\\' . $target, '\\');
        }
        return Atomik_Model_Descriptor_Builder::getBase($className);
    }
}