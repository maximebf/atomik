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

/**
 * @package Atomik
 * @subpackage Model
 */
abstract class Atomik_Model
{
    /** @var Atomik_Model_Descriptor */
    private $_descriptor;
    
    /**
     * @param array $data
     */
    public function __construct($data = array())
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
    
    /**
     * @return Atomik_Model_Descriptor
     */
    public function getDescriptor()
    {
        if ($this->_descriptor === null) {
            $this->_descriptor = Atomik_Model_Descriptor::factory(get_class($this));
        }
        return $this->_descriptor;
    }
    
    /**
     * @return bool
     */
    public function isNew()
    {
        $id = $this->getDescriptor()->getIdentifierField()->getName();
        return !property_exists($this, $id) || $this->{$id} === null;
    }
    
    /**
     * Sets a property of this object
     * 
     * @param string $name
     * @param string $value
     */
    public function setProperty($name, $value)
    {
        $this->{$name} = $value;
    }
    
    /**
     * Returns the value of a property of this object
     * 
     * If the property represents an association, it will be loaded.
     * Order by and limit clauses can be specified in the same way as
     * in Atomik_Model_Query
     * 
     * @param string $name
     * @param mixed $orderBy
     * @param mixed $limit
     * @return string
     */
    public function getProperty($name, $orderBy = null, $limit = null)
    {
	    if (!$this->isNew() && $this->getDescriptor()->hasAssociation($name) && 
	        $this->{$name} === null) {
                $this->getDescriptor()
                     ->getAssociation($name)
                     ->load($this, $orderBy, $limit);
	    }
	    
	    if (property_exists($this, $name)) {
            return $this->{$name};
	    }
	    
	    return null;
    }
	
	/**
	 * Magid method to add getters and setters for properties
	 * 
	 * @param string $method
	 * @param array $args
	 */
	public function __call($method, $args)
	{
	    if (!preg_match('/^(get|set)(.+)$/', $method, $matches)) {
	        return;
	    }
	    
	    $accessor = $matches[1];
	    $property = $matches[2];
	    $property{0} = strtolower($property{0});
	    $method = $accessor . 'Property';
	    array_unshift($args, $property);
	    
	    return call_user_func_array(array($this, $method), $args);
	}
	
	/**
	 * Checks if the model if valid using properties validators
	 * 
	 * @return bool
	 */
	public function isValid()
	{
	    $this->_validationMessages = array();
		$descriptor = $this->getDescriptor();
		$success = true;
		
		foreach ($descriptor->getFields() as $field) {
		    $value = $this->getProperty($field->getName());
		    if (!$field->isValid($value)) {
		        $this->_validationMessages[] = $field->getValidationMessage();
		        $success = false;
		    }
		}
	    
	    return $success;
	}
	
	/**
	 * Returns messages from the last validation
	 * 
	 * @return array of string
	 */
	public function getValidationMessages()
	{
	    return $this->_validationMessages;
	}
	
	/**
	 * Saves the model to the database
	 *
	 * @param bool $validate
	 */
	public function save($validate = true)
	{
		$descriptor = $this->getDescriptor();
		$db = $descriptor->getDb();
		$persister = $descriptor->getPersister();
		$useTransaction = !$db->isInTransaction();
		
	    if ($validate && !$this->isValid()) {
		    require_once 'Atomik/Model/Exception.php';
	        throw new Atomik_Model_Exception("'{$descriptor->getName()}' failed to validate");
	    }
		
	    $useTransaction && $db->beginTransaction();
		
		try {
		    $descriptor->notify('BeforeSave', $this);
		    
    		foreach ($descriptor->getAssociations() as $assoc) {
    		    if (!$assoc->isMany()) {
    		        $assoc->save($this);
    		    }
    		}
    		
    		if ($this->isNew()) {
    			$persister->insert($this);
    		} else {
    			$persister->update($this);
    		}
    		
    		foreach ($descriptor->getAssociations() as $assoc) {
    		    if ($assoc->isMany()) {
    		        $assoc->save($this);
    		    }
    		}
		    
		    $descriptor->notify('AfterSave', $this);
    		$useTransaction && $db->commit();
		    
		} catch (Atomik_Db_Exception $e) {
		    $useTransaction && $db->rollback();
		    throw $e;
		    
		} catch (Atomik_Model_Exception $e) {
		    $useTransaction && $db->rollback();
		    throw $e;
		}
	}
	
	/**
	 * Deletes the model from the database
	 */
	public function delete()
	{
		$descriptor = $this->getDescriptor();
		
		$descriptor->notify('BeforeDelete', $this);
		
		$descriptor->getPersister()->delete($this);
		
		$descriptor->notify('AfterDelete', $this);
	}
}
