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

/** Atomik_Model_Adapter_Interface */
require_once 'Atomik/Model/Adapter/Interface.php';

/** Atomik_Model */
require_once 'Atomik/Model.php';

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

/**
 * Stores models as files
 * 
 * @package Atomik
 * @subpackage Model
 */
class FileModelAdapter implements Atomik_Model_Adapter_Interface
{
	/**
	 * Singleton instance
	 *
	 * @var FileModelAdapter
	 */
	protected static $_instance;
	
	/**
	 * Gets the singleton
	 *
	 * @return FileModelAdapter
	 */
	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Finds many models
	 *
	 * @param Atomik_Model_Builder $builder
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return array
	 */
	public function findAll(Atomik_Model_Builder $builder, $where = null, $orderBy = '', $limit = '')
	{
		$files = array();
		$dir = $builder->getMetadata('dir');
		$iterator = new DirectoryIterator($dir);
		foreach ($iterator as $file) {
			
		}
		return $files;
	}
	
	/**
	 * Finds one model
	 *
	 * @param Atomik_Model_Builder $builder
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return Atomik_Model
	 */
	public function find(Atomik_Model_Builder $builder, $where, $orderBy = '', $limit = '')
	{
		$dir = $builder->getMetadata('dir');
		$file = $dir . $this->getFilename($builder, $where);
		
		if (!file_exists($file)) {
			return null;
		}
		
		$values = $where;
		
		if (($contentKey = $builder->getMetadata('content', null)) !== null) {
			$contentKey = $builder->getMetadata('content');
			$values[$contentKey] = file_get_contents($file);
		}
		
		return $builder->createInstance($values);
	}
	
	/**
	 * Saves a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function save(Atomik_Model $model)
	{
		return true;
	}
	
	/**
	 * Deletes a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function delete(Atomik_Model $model)
	{
		return true;
	}
	
	protected function getFilename($builder, $data)
	{
		if (($path = $builder->getMetadata('path', null)) === null) {
			throw new Atomik_Model_Exception('Missing path key in ' . $builder->getName() . ' model');
		}
		foreach ($data as $key => $value) {
			$path = str_replace(':' . $key, $value, $path);
		}
		return $path;
	}
}