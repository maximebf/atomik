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

/** Atomik_Model_Hydrator */
require_once 'Atomik/Model/Hydrator.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Hydrator_Joined extends Atomik_Model_Hydrator
{
	public function hydrate($data)
	{
	    $descriptor = $this->_descriptor;
	    
	    if (!$descriptor->hasParent()) {
	        $id = $data[$descriptor->getIdentifierField()->getName()];
	        $className = $data[$descriptor->getDescriminatorField()->getName()];
	        $descriptor = Atomik_Model_Descriptor::factory($className);
	        $data = array_merge($data, $this->_getSubclassData($descriptor, $id));
	    } else {
		    $className = $this->_descriptor->getName();
	    }
	    
		$instance = new $className();
		
		foreach ($descriptor->getFields() as $field) {
		    if (isset($data[$field->getName()])) {
    		    $value = $field->getType()->filterInput($data[$field->getName()]);
    		    $instance->setProperty($field->getName(), $value);
		    }
		}
		
		return $instance;
	}
	
	protected function _getSubclassData($descriptor, $id)
	{
	    return $descriptor->getDb()->q()
	            ->select()
	            ->from($descriptor->getTableName())
	            ->where(array($descriptor->getIdentifierField()->getName() => $id))
	            ->limit(1)
	            ->execute()
	            ->fetch();
	}
}