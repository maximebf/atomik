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

/** Atomik_Model_Descriptor_Factory */
require_once 'Atomik/Model/Descriptor/Factory.php';

/**
 * Reads the doc comments of a class and its properties and generates a model descriptor
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Descriptor_ClassMetadata
{
	/**
	 * @var array
	 */
	private static $_cache = array();
	
	/**
	 * Reads metadata from a class doc comments and creates a descriptor object
	 * 
	 * @param	string	$className
	 * @return 	Atomik_Model_Descriptor
	 */
	public static function read($className)
	{
		$descriptor = self::_getBaseDescriptor($className);
		
		// extract references
		$references = array();
		if ($descriptor->hasOption('has')) {
			$references = (array) $descriptor->getOption('has');
			$descriptor->removeOption('has');
		}
		
		// adds references
		foreach ($references as $referenceString) {
			self::addReferenceFromString($descriptor, $referenceString);
		}
		
		// extract behaviours
		$behaviours = array();
		if ($descriptor->hasOption('act-as')) {
			$behaviours = (array) $descriptor->getOption('act-as');
			$descriptor->removeOption('act-as');
		}
		
		// adds behaviours
		foreach ($behaviours as $behaviourString) {
			foreach (explode(',', $behaviourString) as $behaviour) {
				$descriptor->getBehaviourBroker()->addBehaviour(
					Atomik_Model_Behaviour_Factory::factory(trim($behaviour)));
			}
		}
		
		return $descriptor;
	}
	
	/**
	 * Returns a descriptor without the references
	 * 
	 * @param	string	$className
	 * @return 	Atomik_Model_Descriptor
	 */
	private static function _getBaseDescriptor($className)
	{
		if (isset(self::$_cache[$className])) {
			return self::$_cache[$className];
		}
		
		if (!class_exists($className)) {
			require_once 'Atomik/Model/Descriptor/Exception.php';
			throw new Atomik_Model_Descriptor_Exception('Class ' . $className . ' not found');
		}
		
		$class = new ReflectionClass($className);
		$descriptor = new Atomik_Model_Descriptor($className, $className);
		
		foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
			$propData = self::getMetadataFromDocBlock($prop->getDocComment());
			
			// jump to the next property if there is the ignore tag
			if (isset($propData['ignore'])) {
				continue;
			}
			
			$type = 'string';
			if (isset($propData['var'])) {
				$type = $propData['var'];
				unset($propData['var']);
			} else if (!isset($propData['length'])) {
				$propData['length'] = 255;
			}
			
			$field = Atomik_Model_Field_Factory::factory($type, $prop->getName(), $propData);
			$descriptor->addField($field);
		}
		
		$options = self::getMetadataFromDocBlock($class->getDocComment());
		
		// sets the adapter
		if (isset($options['table'])) {
			$descriptor->tableName = $options['table'];
			unset($options['table']);
		}
		
		// use the remaining metadatas as options
		$descriptor->setOptions($options);
		
		if (($parentClass = $class->getParentClass()) != null) {
			if ($parentClass->getName() != 'Atomik_Model' && $parentClass->isSubclassOf('Atomik_Model')) {
				$descriptor->setParentModel($parentClass->getName());
			}
		}
		
		self::$_cache[$className] = $descriptor;
		return $descriptor;
	}
	
	/**
	 * Retreives metadata tags (i.e. the one starting with @) from a doc block
	 *
	 * @param 	string 	$doc
	 * @return 	array
	 */
	public static function getMetadataFromDocBlock($doc)
	{
		$metadata = array();
		preg_match_all('/@(.+)$/mU', $doc, $matches);
		
		for ($i = 0, $c = count($matches[0]); $i < $c; $i++) {
			if (($separator = strpos($matches[1][$i], ' ')) !== false) {
				$key = trim(substr($matches[1][$i], 0, $separator));
				$value = trim(substr($matches[1][$i], $separator + 1));
				// boolean
				if ($value == 'false') {
					$value = false;
				} else if ($value == 'true') {
					$value = true;
				}
			} else {
				$key = trim($matches[1][$i]);
				$value = true;
			}
			
			if (isset($metadata[$key])) {
				if (!is_array($metadata[$key])) {
					$metadata[$key] = array($metadata[$key]);
				}
				$metadata[$key][] = $value;
			} else {
				$metadata[$key] = $value;
			}
		}
		
		return $metadata;
	}
	
	/**
	 * Builds a reference object from a string and adds it to the descriptor. The string should follow the following pattern:
	 * (one|many) foreignModel [as property] [using localModel.localField = foreignModel.foreignField] [order by field] [limit offset, length]
	 *
	 * @param	Atomik_Model_Descriptor	$descriptor
	 * @param 	string 					$string
	 * @return 	Atomik_Model_Descriptor_Reference
	 */
	public static function addReferenceFromString(Atomik_Model_Descriptor $descriptor, $string)
	{
		$regexp = '/(?P<type>one|many|parent)\s+(?P<target>.+)((\sas\s(?P<as>.+))|)((\svia\s(?P<viatype>table|model|)\s(?P<via>.+))|)'
				. '((\susing\s(?P<using>.+))|)((\sorder by\s(?P<order>.+))|)((\slimit\s(?P<limit>.+))|)$/U';
				
		if (!preg_match($regexp, $string, $matches)) {
			require_once 'Atomik/Model/Descriptor/Exception.php';
			throw new Atomik_Model_Descriptor_Exception('Reference string is malformed: ' . $string);
		}
		
		// type and target
		$type = $matches['type'];
		$target = trim($matches['target']);
		
		// name
		$name = $target;
		if (isset($matches['as']) && !empty($matches['as'])) {
			$name = trim($matches['as']);
		}
		
		$reference = new Atomik_Model_Descriptor_Reference($name, $type);
		
		// via
		if (isset($matches['via']) && !empty($matches['via'])) {
			// @TODO support via in model references
		}
		
		// fields
		if (isset($matches['using']) && !empty($matches['using'])) {
			list($sourceField, $targetField) = self::getReferenceFieldsFromString($matches['using'], $descriptor->name);
		} else {
			list($sourceField, $targetField) = self::getReferenceFields($descriptor, $target, $type);
		}
		$reference->sourceField = $sourceField;
		$reference->targetField = $targetField;
		
		// order by
		if (isset($matches['order']) && !empty($matches['order'])) {
			$reference->query->orderBy($matches['order']);
		}
		
		// limit
		if (isset($matches['limit']) && !empty($matches['limit'])) {
			$reference->query->limit($matches['limit']);
		}
		
		$reference->target = $target;
		$descriptor->addReference($reference);
		return $reference;
	}
	
	/**
	 * Returns reference fields depending on the type of reference
	 * 
	 * @param	Atomik_Model_Descriptor	$descriptor
	 * @param	string					$targetName
	 * @param	string					$type
	 * @return 	array					array(sourceField, targetField)
	 */
	public static function getReferenceFields(Atomik_Model_Descriptor $descriptor, $targetName, $type)
	{
		$targetDescriptor = self::_getBaseDescriptor($targetName);
		
		if ($type == Atomik_Model_Descriptor_Reference::HAS_PARENT) {
			// targetModel.targetPrimaryKey = sourceModel.targetModel_targetPrimaryKey
			$targetField = $targetDescriptor->getPrimaryKeyField()->name;
			$sourceField = strtolower($targetName) . '_' . $targetField;
			
		} else if ($type == Atomik_Model_Descriptor_Reference::HAS_ONE) {
			// targetModel.sourceModel_sourcePrimaryKey = sourceModel.sourcePrimaryKey
			$sourceField = $descriptor->getPrimaryKeyField()->name;
			$targetField = strtolower($descriptor->name) . '_' . $sourceField;
			
		} else {
			$targetDescriptor = Atomik_Model_Descriptor_Factory::get($targetName);
			
			// HAS_MANY
			// searching through the target model references for one pointing back to this model
			$parentRefs = $targetDescriptor->getReferences();
			$found = false;
			foreach ($parentRefs as $parentRef) {
				if ($parentRef->isHasParent() && $parentRef->isTarget($descriptor->name)) {
					$sourceField = $parentRef->targetField;
					$targetField = $parentRef->sourceField;
					$found = true;
					break;
				}
			}
			if (!$found) {
				require_once 'Atomik/Model/Descriptor/Exception.php';
				throw new Atomik_Model_Descriptor_Exception('No back reference in ' . $targetName . ' for ' . $descriptor->name);
			}
		}
		
		return array($sourceField, $targetField);
	}
	
	/**
	 * Returns reference fields computed from a string which should follow 
	 * the pattern sourceMode.sourceField = targetModel.targetField (or vice versa)
	 * 
	 * @param 	string	$string
	 * @param 	string	$sourceName
	 * @return 	array	array(sourceField, targetField)
	 */
	public static function getReferenceFieldsFromString($string, $sourceName)
	{
		if (!preg_match('/(.+)\.(.+)\s(=)\s(.+)\.(.+)/', $string, $matches)) {
			require_once 'Atomik/Model/Descriptor/Exception.php';
			throw new Atomik_Model_Descriptor_Exception('Using statement for reference is malformed: ' . $string);
		}
		
		if ($matches[1] == $sourceName) {
			return array($matches[2], $matches[5]);
		}
		return array($matches[5], $matches[2]);
	}
}