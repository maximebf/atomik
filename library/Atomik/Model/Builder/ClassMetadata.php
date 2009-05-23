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

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

/** Atomik_Model_Builder_Factory */
require_once 'Atomik/Model/Builder/Factory.php';

/**
 * Reads the doc comments of a class and its properties and generates a model builder
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Builder_ClassMetadata
{
	/**
	 * @var array
	 */
	private static $_cache = array();
	
	/**
	 * Reads metadata from a class doc comments and creates a builder object
	 * 
	 * @param	string	$className
	 * @return 	Atomik_Model_Builder
	 */
	public static function read($className)
	{
		$builder = self::_getBaseBuilder($className);
		
		// removes the has key to set options before references
		$references = array();
		if ($builder->hasOption('has')) {
			$references = (array) $builder->getOption('has');
			$builder->removeOption('has');
		}
		
		// adds references
		foreach ($references as $referenceString) {
			self::addReferenceFromString($builder, $referenceString);
		}
		
		return $builder;
	}
	
	/**
	 * Returns a builder without the references
	 * 
	 * @param	string	$className
	 * @return 	Atomik_Model_Builder
	 */
	private static function _getBaseBuilder($className)
	{
		if (isset(self::$_cache[$className])) {
			return self::$_cache[$className];
		}
		
		$class = new ReflectionClass($className);
		$builder = new Atomik_Model_Builder($className, $className);
		
		foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
			$propData = self::getMetadataFromDocBlock($prop->getDocComment());
			
			// jump to the next property if there is the ignore tag
			if (isset($propData['ignore'])) {
				continue;
			}
			
			$field = new Atomik_Model_Builder_Field($prop->getName(), $propData);
			$builder->addField($field);
		}
		
		$options = self::getMetadataFromDocBlock($class->getDocComment());
		
		// sets the adapter
		if (isset($options['adapter'])) {
			$adapterName = $options['adapter'];
			$builder->setAdapter(Atomik_Model_Adapter_Factory::factory($adapterName));
			unset($options['adapter']);
		}
		
		// use the remaining metadatas as options
		$builder->setOptions($options);
		
		if (($parentClass = $class->getParentClass()) != null) {
			if ($parentClass->getName() != 'Atomik_Model' && $parentClass->isSubclassOf('Atomik_Model')) {
				$builder->setParentModel($parentClass->getName());
			}
		}
		
		self::$_cache[$className] = $builder;
		return $builder;
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
	 * Builds a reference object from a string and adds it to the builder. The string should follow the following pattern:
	 * (one|many) foreignModel [as property] [using localModel.localField = foreignModel.foreignField] [order by field] [limit offset, length]
	 *
	 * @param	Atomik_Model_Builder	$builder
	 * @param 	string 					$string
	 * @return 	Atomik_Model_Builder_Reference
	 */
	public static function addReferenceFromString(Atomik_Model_Builder $builder, $string)
	{
		$regexp = '/(?P<type>one|many|parent)\s+(?P<target>.+)((\sas\s(?P<as>.+))|)((\svia\s(?P<via>.+))|)'
				. '((\susing\s(?P<using>.+))|)((\sorder by\s(?P<order>.+))|)((\slimit\s(?P<limit>.+))|)$/U';
				
		if (!preg_match($regexp, $string, $matches)) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Reference string is malformed: ' . $string);
		}
		
		// type and target
		$type = $matches['type'];
		$target = trim($matches['target']);
		
		// name
		$name = $target;
		if (isset($matches['as']) && !empty($matches['as'])) {
			$name = trim($matches['as']);
		}
		
		$reference = new Atomik_Model_Builder_Reference($name, $type);
		$reference->source = $builder->name;
		$reference->target = $target;
		
		// via
		if (isset($matches['via']) && !empty($matches['via'])) {
			// @TODO support via in model references
		}
		
		// fields
		if (isset($matches['using']) && !empty($matches['using'])) {
			list($sourceField, $targetField) = self::getReferenceFieldsFromString($matches['using'], $reference);
		} else {
			list($sourceField, $targetField) = self::getReferenceFields($builder, $reference);
		}
		$reference->sourceField = $sourceField;
		$reference->targetField = $targetField;
		
		// order by
		if (isset($matches['order']) && !empty($matches['order'])) {
			$reference->orderBy = $matches['order'];
		}
		
		// limit
		if (isset($matches['limit']) && !empty($matches['limit'])) {
			$reference->limit = $matches['limit'];
		}
		
		$builder->addReference($reference);
		return $reference;
	}
	
	/**
	 * Returns reference fields depending on the type of reference
	 * 
	 * @param	Atomik_Model_Builder			$builder
	 * @param	Atomik_Model_Builder_Reference
	 * @return 	array							array(sourceField, targetField)
	 */
	public static function getReferenceFields(Atomik_Model_Builder $builder, Atomik_Model_Builder_Reference $reference)
	{
		$targetBuilder = self::_getBaseBuilder($reference->target);
		
		if ($reference->isHasParent()) {
			// targetModel.targetPrimaryKey = sourceModel.targetModel_targetPrimaryKey
			$targetField = $targetBuilder->getPrimaryKeyField()->name;
			$sourceField = strtolower($reference->target) . '_' . $targetField;
			
		} else if ($reference->isHasOne()) {
			// targetModel.sourceModel_sourcePrimaryKey = sourceModel.sourcePrimaryKey
			$sourceField = $builder->getPrimaryKeyField()->name;
			$targetField = strtolower($builder->name) . '_' . $sourceField;
			
		} else {
			$targetBuilder = Atomik_Model_Builder_Factory::get($reference->target);
			
			// HAS_MANY
			// searching through the target model references for one pointing back to this model
			$parentsRef = $targetBuilder->getReferences();
			$found = false;
			foreach ($parentsRef as $parentRef) {
				if ($parentRef->isHasParent() && $parentRef->isTarget($builder->name)) {
					$sourceField = $parentRef->targetField;
					$targetField = $parentRef->sourceField;
					$found = true;
					break;
				}
			}
			if (!$found) {
				require_once 'Atomik/Model/Exception.php';
				throw new Atomik_Model_Exception('No back reference in ' . $reference->target . ' for ' . $builder->name);
			}
		}
		
		return array($sourceField, $targetField);
	}
	
	/**
	 * Returns reference fields computed from a string which should follow 
	 * the pattern sourceMode.sourceField = targetModel.targetField (or vice versa)
	 * 
	 * @param 	string	$string
	 * @return 	array			array(sourceField, targetField)
	 */
	public static function getReferenceFieldsFromString($string, Atomik_Model_Builder_Reference $reference)
	{
		if (!preg_match('/(.+)\.(.+)\s(=)\s(.+)\.(.+)/', $string, $matches)) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Using statement for reference is malformed: ' . $string);
		}
		
		if ($matches[1] == $reference->source) {
			return array($matches[2], $matches[5]);
		}
		return array($matches[5], $matches[2]);
	}
}