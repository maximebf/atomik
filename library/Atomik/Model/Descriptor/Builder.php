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

/** Atomik_Model_Descriptor */
require_once 'Atomik/Model/Descriptor.php';

/** Atomik_Annotation */
require_once 'Atomik/Annotation.php';

/** Atomik_Model_Descriptor_Annotation_Association */
require_once 'Atomik/Model/Descriptor/Annotation/Association.php';

/** Atomik_Model_Descriptor_Annotation_Field */
require_once 'Atomik/Model/Descriptor/Annotation/Field.php';

/** Atomik_Model_Descriptor_Annotation_Form */
require_once 'Atomik/Model/Descriptor/Annotation/Form.php';

/** Atomik_Model_Descriptor_Annotation_Id */
require_once 'Atomik/Model/Descriptor/Annotation/Id.php';

/** Atomik_Model_Descriptor_Annotation_Id */
require_once 'Atomik/Model/Descriptor/Annotation/Inheritance.php';

/** Atomik_Model_Descriptor_Annotation_Model */
require_once 'Atomik/Model/Descriptor/Annotation/Model.php';

/** Atomik_Model_Descriptor_Annotation_Validate */
require_once 'Atomik/Model/Descriptor/Annotation/Validate.php';

/** Atomik_Model_Behaviour_Orderable */
require_once 'Atomik/Model/Behaviour/Orderable.php';

/** Atomik_Model_Behaviour_Publishable */
require_once 'Atomik/Model/Behaviour/Publishable.php';

/** Atomik_Model_Behaviour_Sluggable */
require_once 'Atomik/Model/Behaviour/Sluggable.php';

/** Atomik_Model_Behaviour_Timestampable */
require_once 'Atomik/Model/Behaviour/Timestampable.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Descriptor_Builder
{
	/** @var array */
	private static $_cache = array();
	
	/** @var array */
	private static $_built = array();
	
	/**
	 * Reads metadata from a class annotations and creates a descriptor object
	 * 
	 * @param	string	$className
	 * @return 	Atomik_Model_Descriptor
	 */
	public function build($className)
	{
		if (!isset(self::$_built[$className])) {
    	    $class = new ReflectionAnnotatedClass($className);
    		$descriptor = self::getBase($class);
    		
    		// applying associations
    		foreach ($class->getProperties() as $prop) {
    		    if (!$prop->isPublic() && $prop->hasAnnotation('Association')) {
            		foreach ($prop->getAnnotations() as $annotation) {
            		    if ($annotation instanceof Atomik_Model_Descriptor_Annotation) {
            		        $annotation->apply($descriptor, $prop);
            		    }
            		}
    		    }
    		}
    		
		    self::$_built[$className] = $descriptor;
		}
		
		return self::$_built[$className];
	}
	
	/**
	 * Finishes building models which have just been initialized
	 */
	public function buildRemainings()
	{
	    foreach (array_diff_key(self::$_cache, self::$_built) as $key => $value) {
	        self::build($key);
	    }
	}
	
	/**
	 * Returns a descriptor without the associations
	 * 
	 * @param	string	$className
	 * @return 	Atomik_Model_Descriptor
	 */
	public static function getBase($className)
	{
	    if ($className instanceof ReflectionClass) {
	        $class = $className;
	        $className = $className->getName();
	    }
	    
		if (isset(self::$_cache[$className])) {
			return self::$_cache[$className];
		}
		
		if (!class_exists($className)) {
			require_once 'Atomik/Model/Descriptor/Exception.php';
			throw new Atomik_Model_Descriptor_Exception("Class '$className' not found");
		}
		
		$descriptor = new Atomik_Model_Descriptor($className, $className);
		if (!isset($class)) {
		    $class = new ReflectionAnnotatedClass($className);
		}
		
		$annotations = $class->getAllAnnotations();
		
		foreach ($class->getAnnotations() as $annotation) {
		    if ($annotation instanceof Atomik_Model_Descriptor_Annotation) {
		        $annotation->apply($descriptor, $class);
		    }
		}
		
		if (($parent = $class->getParentClass()) !== false) {
		    $parent = self::getBase($parent->getName());
		    if ($parent->getInheritanceType() != 'none') {
		        $descriptor->setParent($parent);
		        foreach ($parent->getBehaviours() as $behaviour) {
		            $behaviour->apply($descriptor, $class);
		        }
		    }
		}
		
		// applying fields
		foreach ($class->getProperties() as $prop) {
		    if (!$prop->isPublic() && !$prop->hasAnnotation('Association')) {
        		foreach ($prop->getAnnotations() as $annotation) {
        		    if ($annotation instanceof Atomik_Model_Descriptor_Annotation) {
        		            $annotation->apply($descriptor, $prop);
        		    }
        		}
		    }
		}
		
		if ($descriptor->getIdentifierField() === null) {
		    $idField = Atomik_Model_Field::factory('id', 'int');
		    $descriptor->mapProperty($idField);
		    $descriptor->setIdentifierField($idField);
		}
		
		self::$_cache[$className] = $descriptor;
		return $descriptor;
	}
}