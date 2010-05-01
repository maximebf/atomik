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
 * @subpackage Db
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Model_Exporter
{
    /** @var Atomik_Db_Instance */
    protected $_db;
    
    /** @var array of Atomik_Model_Descriptor */
    protected $_descriptors = array();
    
    /** @var bool */
    protected $_dropBeforeCreate = true;
    
    /**
     * @param Atomik_Db_Instance $db
     */
    public function __construct(Atomik_Db_Instance $db)
    {
        $this->_db = $db;
    }
    
    /**
     * @param Atomik_Db_Instance $db
     */
    public function setDb(Atomik_Db_Instance $db)
    {
        $this->_db = $db;
    }
    
    /**
     * @return Atomik_Db_Instance
     */
    public function getDb()
    {
        return $this->_db;
    }
    
    /**
     * @param array $descriptors array of Atomik_Model_Descriptor
     */
    public function addDescriptors(array $descriptors)
    {
        array_map(array($this, 'addDescriptor'), $descriptors);
    }
    
    /**
     * @param Atomik_Model_Descriptor $descriptor
     */
    public function addDescriptor(Atomik_Model_Descriptor $descriptor)
    {
        $this->_descriptors[] = $descriptor;
    }
    
    /**
     * @return array of Atomik_Model_Descriptor
     */
    public function getDescriptors()
    {
        return $this->_descriptors;
    }
    
    /**
     * Sets whether or not to drop tables before creation
     * 
     * @param bool $enabled
     */
    public function dropBeforeCreate($enabled = true)
    {
        $this->_dropBeforeCreate = $enabled;
    }
    
    /**
     * @return bool
     */
    public function isDropingBeforeCreate()
    {
        return $this->_dropBeforeCreate;
    }
    
    /**
     * @return Atomik_Db_Schema
     */
    public function buildSchema()
    {
        $schema = new Atomik_Db_Schema($this->_db);
    
        foreach ($this->_descriptors as $desc) {
            $this->_buildTable($schema, $desc);
        }
        
        return $schema;
    }
    
    /***
     * @return string
     */
    public function getSql()
    {
        $schema = $this->buildSchema();
        $sql = '';
        
        if ($this->_dropBeforeCreate) {
            foreach ($schema->getTables() as $table) {
                $sql .= $schema->getGenerator()->generateDrop($table);
            }
        }
        
        $sql .= $schema->toSql();
        
        foreach ($this->_descriptors as $desc) {
            $desc->notify('AfterExport', $sql);
        }
        
        return $sql;
    }
    
    /**
     * @param Atomik_Db_Schema $schema
     * @param Atomik_Model_Descriptor $descriptor
     * @return Atomik_Db_Schema_Table
     */
    protected function _getTable(Atomik_Db_Schema $schema, Atomik_Model_Descriptor $descriptor)
    {
        if (!$schema->hasTable($descriptor->getTableName())) {
		    $schema->createTable($descriptor->getTableName());
        }
        return $schema->getTable($descriptor->getTableName());
    }
    
    /**
     * @param Atomik_Db_Schema $schema
     * @param Atomik_Model_Descriptor $descriptor
     */
	protected function _buildTable(Atomik_Db_Schema $schema, Atomik_Model_Descriptor $descriptor)
	{
		$table = $this->_getTable($schema, $descriptor);
		
		foreach ($descriptor->getFields() as $field) {
		    if ($descriptor->getIdentifierField() != $field && $field->isInherited()) {
		        continue;
		    }
		    
			$column = $table->createColumn($field->getName(), $field->getType());
			
			if ($descriptor->getIdentifierField() == $field) {
				$table->setPrimaryKey($column);
				if ($field->getType()->getName() == 'int') {
				    $column->setOption('auto-increment', true);
				}
			} else if ($descriptor->isFieldPartOfAssociation($field)) {
			    $table->createIndex($column->getName() . '_idx', $column);
			}
		}
		
		foreach ($descriptor->getAssociations() as $assoc) {
		    if ($assoc instanceof Atomik_Model_Association_ManyToMany) {
		        $via = $schema->createTable($assoc->getViaTable());
                $source = $via->createColumn($assoc->getViaSourceField(), Atomik_Db_Type::factory('int'));
		        $target = $via->createColumn($assoc->getViaTargetField(), Atomik_Db_Type::factory('int'));
		        $via->createIndex($source->getName() . '_idx', $source);
		        $via->createIndex($target->getName() . '_idx', $target);
		    }
		}
		
		$descriptor->notify('BeforeExport', $schema);
	}
}