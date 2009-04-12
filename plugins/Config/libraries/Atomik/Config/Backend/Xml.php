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
 * @subpackage Config
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Config_Backend_Interface */
require_once 'Atomik/Config/Backend/Interface.php';

/**
 * @package Atomik
 * @subpackage Config
 */
class Atomik_Config_Backend_Xml implements Atomik_Config_Backend_Interface
{
	/**
	 * @var string
	 */
	protected $_filename;
	
	/**
	 * @var DOMDocument
	 */
	protected $_dom;
	
	/**
	 * @var DOMXPath
	 */
	protected $_xpath;
	
	/**
	 * Constructor
	 * 
	 * @param	string	$filename
	 */
	public function __construct($filename = null)
	{
		if ($filename !== null) {
			$this->setFilename($filename);
		}
	}
	
	/**
	 * Sets the file to use
	 * 
	 * @param	string	$filename
	 */
	public function setFilename($filename)
	{
		$this->_filename = $filename;
		
		$this->_dom = new DOMDocument();
		
		try {
			$this->_dom->load($filename);
		} catch (Exception $e) {
			$this->_dom->appendChild($this->_dom->createElement('config'));
		}
		
		$this->_xpath = new DOMXpath($this->_dom);
	}
	
	/**
	 * Returns the file being used
	 * 
	 * @return string
	 */
	public function getFilename()
	{
		return $this->_filename;
	}
	
	/**
	 * Returns the current DOMDocument object
	 * 
	 * @return DOMDocument
	 */
	protected function _getDom()
	{
		if ($this->_dom === null) {
			require_once 'Atomik/Config/Exception.php';
			throw new Atomik_Config_Exception('No filename specified for the xml backend');
		}
		return $this->_dom;
	}
	
	/**
	 * Returns a dimensionized array of all keys from the file
	 * 
	 * @return array
	 */
	public function getAll()
	{
		$simpleXml = simplexml_import_dom($this->_getDom()->documentElement);
		return $this->_nodeToArray($simpleXml);
	}
	
	/**
	 * Converts a SimpleXMLElement to an array
	 * 
	 * @param	SimpleXMLElement	$node
	 * @return	array
	 */
	protected function _nodeToArray(SimpleXMLElement $node)
	{
		$array = array();
		
		foreach ($node->children() as $child) {
			$key = (string) $child['name'];
			
			if (count($child->children()) > 0) {
				$array[$key] = $this->_nodeToArray($child);
			} else {
				$value = (string) $child;
				$array[$key] = unserialize($value);
			}
		}
		
		return $array;
	}
	
	/**
	 * Sets a key in the file
	 * 
	 * @param	string	$key
	 * @param 	mixed	$value
	 */
	public function set($key, $value)
	{
		$this->_setRecursive($key, $value);
		$this->_getDom()->save($this->_filename);
	}
	
	/**
	 * Sets a key in the file
	 * 
	 * @param	string	$key
	 * @param 	mixed	$value
	 * @param 	DOMNode	$parentNode
	 */
	protected function _setRecursive($key, $value, DOMNode $parentNode = null)
	{
		if ($parentNode === null) {
			$parentNode = $this->_getDom()->documentElement;
		}
		
		if (strpos($key, '/') !== false) {
			$segments = explode('/', $key);
			$key = array_shift($segments);
			
			// checks if the item with the current key already exists
			$list = $this->_xpath->query('item[@name="' . $key . '"]', $parentNode);
			if ($list->length == 0) {
				// creates it
				$node = $this->_getDom()->createElement('item');
				$node->setAttribute('name', $key);
				$parentNode = $parentNode->appendChild($node);
			} else {
				$parentNode = $list->item(0);
			}
			
			return $this->_setRecursive(implode('/', $segments), $value, $parentNode);
		}
		
		// removes the existing item if any
		$list = $this->_xpath->query('item[@name="' . $key . '"]', $parentNode);
		if ($list->length > 0) {
			$parentNode->removeChild($list->item(0));
		}
		
		$node = $this->_getDom()->createElement('item', serialize($value));
		$node->setAttribute('name', $key);
		
		return $parentNode->appendChild($node);
	}
	
	/**
	 * Deletes a key in the file
	 * 
	 * @param 	string	$key
	 */
	public function delete($key)
	{
		$this->_getDom();
		
		// builds the xpath query
		$xpathSegments = array();
		$segments = explode('/', $key);
		foreach ($segments as $segment) {
			$xpathSegments[] = 'item[@name="' . $segment . '"]';
		}
		
		$list = $this->_xpath->query(implode('/', $xpathSegments));
		if ($list->length > 0) {
			$node = $list->item(0);
			$node->parentNode->removeChild($node);
		}
		
		$this->_getDom()->save($this->_filename);
	}
}