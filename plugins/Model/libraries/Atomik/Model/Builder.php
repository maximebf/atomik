<?php
/**
 * Atomik Framework
 * Copyright (c) 2008 Maxime Bouroumeau-Fuseau
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
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Model */
require_once 'Atomik/Model.php';

/** Atomik_Model_Adapter_Interface */
require_once 'Atomik/Model/Adapter/Interface.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Builder
{
	/**
	 * Model class name
	 *
	 * @var string
	 */
	protected $_class;
	
	/**
	 * Model name
	 *
	 * @var string
	 */
	protected $_name;
	
	/**
	 * @var array
	 */
	protected $_adapters = array();
	
	/**
	 * @var array
	 */
	protected $_metadata = array();
	
	/**
	 * @var array
	 */
	protected static $_metadataCache = array();
	
	/**
	 * @var Atomik_Model_Adapter_Interface
	 */
	private static $_defaultAdapter;
	
	/**
	 * Sets the default adapter 
	 *
	 * @param string|Atomik_Model_Adapter_Interface $adapter
	 */
	public static function setDefaultAdapter(Atomik_Model_Adapter_Interface $adapter)
	{
		self::$_defaultAdapter = $adapter;
	}
	
	/**
	 * Gets the default adapter
	 *
	 * @return Atomik_Model_Adapter_Interface
	 */
	public static function getDefaultAdapter()
	{
		return self::$_defaultAdapter;
	}
	
	/**
	 * Constructor
	 *
	 * @param string $class OPTIONAL
	 * @param bool $isClass OPTIONAL (default true) If False the first parameter will be the model name
	 */
	public function __construct($class = null, $isClass = true)
	{
		if ($class !== null) {
			if ($isClass) {
				$this->_class = is_string($class) ? $class : get_class($class);
				$this->_name = $this->_class;
				$this->_buildMetadata();
			} else {
				$this->_name = (string) $class;
			}
		}
	}
	
	/**
	 * Gets the model's class name
	 *
	 * @return string
	 */
	public function getClass()
	{
		return $this->_class;
	}
	
	/**
	 * Sets the model name
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->_name = $name;
	}
	
	/**
	 * Gets the model name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Sets all adapters
	 *
	 * @param array $adapters
	 */
	public function setAdapters($adapters = null)
	{
		if ($adapters === null) {
			$adapters = array(self::getDefaultAdapter());
		}
		
		$this->_adapters = array();
		foreach ($adapters as $adapter) {
			$this->addAdapter($adapter);
		}
	}
	
	/**
	 * Adds an adapter
	 *
	 * @param Atomik_Model_Adapter_Interface $adapter
	 */
	public function addAdapter(Atomik_Model_Adapter_Interface $adapter)
	{
		if (count($this->_adapters)) {
			/* a prmiary key is needed if multiple adapters are used */
			if (!isset($this->_metadata['primary-key'])) {
				require_once 'Atomik/Model/Exception.php';
				throw new Atomik_Model_Exception('Model has multiple adapters but no primary key: ' . $this->_name);
			}
		}
		
		$this->_adapters[] = $adapter;
	}
	
	/**
	 * Gets all adapters
	 *
	 * @return array
	 */
	public function getAdapters()
	{
		return $this->_adapters;
	}
	
	/**
	 * Sets metadata
	 *
	 * @param array $metadata
	 * @param bool $merge OPTIONAL
	 */
	public function setMetadata($metadata, $merge = true)
	{
		if ($merge) {
			$this->_metadata = array_merge($this->_metadata, $metadata);
		} else {
			$this->_metadata = $metadata;
		}
	}
	
	/**
	 * Gets metadata
	 *
	 * @param string $key OPTIONAL Only retreives one value
	 * @param mixed $default OPTIONAL Default value if the key is not found
	 * @return mixed
	 */
	public function getMetadata($key = null, $default = null)
	{
		if ($key !== null) {
			if (!array_key_exists($key, $this->_metadata)) {
				return $default;
			}
			return $this->_metadata[$key];
		}
		return $this->_metadata;
	}
	
	/**
	 * Creates a model instance
	 *
	 * @see Atomik_Model::__construct()
	 * @param array $values OPTIONAL
	 * @param bool $new OPTIONAL
	 * @return Atomik_Model
	 */
	public function createInstance($values = array(), $new = true)
	{
		if ($this->_class !== null) {
			$class = $this->_class;
			$instance = new $class($values, $new);
		} else {
			$instance = new Atomik_Model($values, $new);
		}
		$instance->setBuilder($this);
		return $instance;
	}
	
	/**
	 * Builds metadata from class doc blocks
	 */
	protected function _buildMetadata()
	{
		if (isset(self::$_metadataCache[$this->_class])) {
			/* using cached metadata */
			$this->_metadata = self::$_metadataCache[$this->_class];
			
		} else {
			$class = new ReflectionClass($this->_class);
			$metadata = $this->_buildMetadataFromDocBlock($class->getDocComment());
			
			$metadata['fields'] = array();
			foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
				/* retreives property metadatas */
				$propData = $this->_buildMetadataFromDocBlock($prop->getDocComment());
				
				/* jump to the next property if there is the ignore tag */
				if (isset($propData['ignore'])) {
					continue;
				}
				
				/* adds the property name into the meta */
				$propData['property'] = $prop->getName();
				if (!isset($propData['name'])) {
					$propData['name'] = $prop->getName();
				}
				
				/* adds the property to the field list */
				$metadata['fields'][] = $propData;
			}
			
			$this->_buildReferences($metadata);
		
			$this->_metadata = $metadata;
			self::$_metadataCache[$this->_class] = $metadata;
		}
		
		if (isset($this->_metadata['adapter'])) {
			if (!is_array($this->_metadata['adapter'])) {
				$this->_metadata['adapter'] = array($this->_metadata['adapter']);
			}
			foreach ($this->_metadata['adapter'] as $adapter) {
				$this->addAdapter(ModelPlugin::getAdapter($adapter));
			}
			
		} else {
			$this->setAdapters();
		}
	}
	
	/**
	 * Retreives metadata tags (i.e. the one starting with @model) from a doc block
	 *
	 * @param string $doc
	 * @return array
	 */
	protected function _buildMetadataFromDocBlock($doc)
	{
		$metadata = array();
		preg_match_all('/@(.+)(\s.+|)$/mU', $doc, $matches);
		
		for ($i = 0, $c = count($matches[0]); $i < $c; $i++) {
			$key = $matches[1][$i];
			$value = trim($matches[2][$i]);
			
			if (isset($metadata[$key]) && !is_array($metadata[$key])) {
				$metadata[$key] = array($metadata[$key]);
			}
			
			if (isset($metadata[$key])) {
				$metadata[$key][] = $value;
			} else {
				$metadata[$key] = $value;
			}
		}
		
		return $metadata;
	}
	
	/**
	 * Builds metadata about references
	 *
	 * @param array $metadata
	 * @return array
	 */
	protected function _buildReferences(&$metadata)
	{
		$metadata['references'] = array();
		$this->_buildReferenceType('has-one', $metadata);
		$this->_buildReferenceType('has-many', $metadata);
		
		foreach ($metadata['references'] as &$ref) {
			if (isset($ref['using'])) {
				continue;
			}
			
			if ($ref['type'] == 'has-one') {
				$ref['using'] = array(
					array('model' => $this->_class, 'field' => strtolower($ref['model']) . '_id'),
					array('model' => $ref['model'], 'field' => 'id')
				);
				continue;
			}
			
			$refBuilder = new self($ref['model']);
			$parentsRef = $refBuilder->getMetadata('references', array());
			foreach ($parentsRef as $parentRef) {
				if ($parentRef['type'] == 'has-one' && $parentRef['model'] == $this->_class) {
					$using = $parentRef['using'];
					break;
				}
			}
			$ref['using'] = $using;
		}
	}
	
	/**
	 * Builds reference table for a given type
	 *
	 * @param string $type
	 * @param array $metadata
	 * @return array
	 */
	protected function _buildReferenceType($type, &$metadata)
	{
		if (!isset($metadata[$type])) {
			$refs = array();	
		} else {
			$refs = $metadata[$type];
		}
		$refs = is_array($refs) ? $refs : array($refs);
		
		foreach ($refs as $rawRef) {
			if (!preg_match('/(.+)((\sas\s(.+))|)((\susing\s(.+))|)$/U', $rawRef, $matches)) {
				require_once 'Atomik/Model/Exception.php';
				throw new Atomik_Model_Exception('Reference metadata is malformed: ' . $rawRef);
			}
			
			$ref = array('type' => $type, 'model' => trim($matches[1]));
			
			/* property name */
			if (isset($matches[4])) {
				$ref['as'] = trim($matches[4]);
			} else {
				$ref['as'] = $ref['model'];
			}
			
			/* using statement */
			if (isset($matches[7])) {
				if (!preg_match('/(.+)\.(.+)\s=\s(.+)\.(.+)/', $matches[7], $matches)) {
					require_once 'Atomik/Model/Exception.php';
					throw new Atomik_Model_Exception('Using statement in reference metadata '
						. 'is malformed: ' . $rawRef);
				}
				$ref['using'] = array(
					array('model' => $matches[1], 'field' => $matches[2]),
					array('model' => $matches[3], 'field' => $matches[4])
				);
			}
			
			$metadata['references'][$ref['as']] = $ref;
		}
		
		unset($metadata[$type]);
	}
}