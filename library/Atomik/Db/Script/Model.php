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

/** Atomik_Db_Script_Interface */
require_once 'Atomik/Db/Script/Interface.php';

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

/** Atomik_Model_Builder_Factory */
require_once 'Atomik/Model/Builder/Factory.php';

/** Atomik_Model_Adapter_Db */
require_once 'Atomik/Model/Adapter/Db.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Script_Model implements Atomik_Db_Script_Interface
{
	/**
	 * @var Atomik_Model_Builder
	 */
	protected $_modelBuilder;
	
	/**
	 * Returns an array of script obtained from a directory
	 * 
	 * @param	string	$dir
	 * @param 	string	$parent
	 * @return 	array
	 */
	public static function getScriptFromDir($dir, $parent = '')
	{
		$scripts = array();
		
		foreach (new DirectoryIterator($dir) as $file) {
			if ($file->isDot() || substr($file->getFilename(), 0, 1) == '.') {
				continue;
			}
			
			$filename = $file->getFilename();
			if (strpos($filename, '.') !== false) {
				$filename = substr($filename, 0, strrpos($filename, '.'));
			}
			$className = trim($parent . '_' . $filename, '_');
			
			if ($file->isDir()) {
				$scripts = array_merge($scripts, self::getScriptFromDir($file->getPathname(), $className));
				continue;
			}
			
			require_once $file->getPathname();
			if (!class_exists($className, false)) {
				continue;
			}
			
			$builder = Atomik_Model_Builder_Factory::get($className);
			$scripts[] = new Atomik_Db_Script_Model($builder);
		}
		
		return $scripts;
	}
	
	/**
	 * Constructor
	 * 
	 * @param	Atomik_Model_Builder	$builder
	 */
	public function __construct(Atomik_Model_Builder $modelBuilder = null)
	{
		$this->_modelBuilder = $modelBuilder;
	}
	
	/**
	 * Sets the model builder
	 * 
	 * @param	Atomik_Model_Builder	$builder
	 */
	public function setModelBuilder(Atomik_Model_Builder $modelBuilder)
	{
		if (get_class($modelBuilder->getAdapter()) != 'Atomik_Model_Adapter_Db') {
			require_once 'Atomik/Db/Script/Exception.php';
			throw new Atomik_Db_Script_Exception('The model builder must use the Db adapter');
		}
		$this->_modelBuilder = $modelBuilder;
	}
	
	/**
	 * Returns the model builder
	 * 
	 * @return	Atomik_Model_Builder
	 */
	public function getModelBuilder()
	{
		return $this->_modelBuilder;
	}
	
	/**
	 * Returns the sql needed to create the table associated to the model
	 */
	public function getSql()
	{
		$table = Atomik_Model_Adapter_Db::getTableNameFromBuilder($this->_modelBuilder);
		$fields = array();
		$primaryKeyField = $this->_modelBuilder->getPrimaryKeyField();
		
		$fields[] = "\t" . $primaryKeyField->name . ' int PRIMARY KEY';
		
		foreach ($this->_modelBuilder->getFields() as $field) {
			if ($field == $primaryKeyField) {
				continue;
			}
			$type = $field->getOption('sql-type', $field->getOption('var', 'varchar(50)'));
			$fields[] = "\t" . $field->name . ' ' . $type;
		}
		
		$sql = sprintf("DROP TABLE IF EXISTS %s;\n", $table);
		$sql .= sprintf("CREATE TABLE %s (\n%s\n);\n", $table, implode(", \n", $fields));
		return $sql;
	}
	
	/**
	 * @see Atomik_Db_Script_Model::getSql()
	 */
	public function __toString()
	{
		return $this->getSql();
	}
}