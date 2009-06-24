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

/** Atomik_Model_Builder_Factory */
require_once 'Atomik/Model/Builder/Factory.php';

/** Atomik_Model_Export */
require_once 'Atomik/Model/Export.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Script_Model implements Atomik_Db_Script_Interface
{
	/**
	 * @var Atomik_Model_Builder
	 */
	protected $_builder;
	
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
	public function __construct(Atomik_Model_Builder $builder = null)
	{
		$this->setModelBuilder($builder);
	}
	
	/**
	 * Sets the model builder
	 * 
	 * @param	Atomik_Model_Builder	$builder
	 */
	public function setModelBuilder(Atomik_Model_Builder $builder)
	{
		$this->_builder = $builder;
	}
	
	/**
	 * Returns the model builder
	 * 
	 * @return	Atomik_Model_Builder
	 */
	public function getModelBuilder()
	{
		return $this->_builder;
	}
	
	/**
	 * Returns the sql needed to create the table associated to the model
	 */
	public function getSql()
	{
		$exporter = new Atomik_Model_Export();
		return $exporter->export($this->_builder);
	}
	
	/**
	 * @see Atomik_Db_Script_Model::getSql()
	 */
	public function __toString()
	{
		return $this->getSql();
	}
}