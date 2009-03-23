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
class Atomik_Model_Adapter_File implements Atomik_Model_Adapter_Interface
{
	protected $_orderBy;
	
	/**
	 * Not supported on this adapter
	 */
	public function query(Atomik_Model_Builder $builder, $query)
	{
		return array();
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
		if ($where === null) {
			$where = array();
		}
		
		$files = array();
		$dir = $this->_getDir($builder);
		$templateFilename = $this->_getFilename($builder, $where);
		
		/* removes the extension from the path */
		$extension = null;
		if (($dot = strrpos($templateFilename, '.')) !== false) {
			$extension = strtolower(substr($templateFilename, $dot + 1));
			$templateFilename = substr($templateFilename, 0, $dot);
		}
		
		$segments = explode('/', $templateFilename);
		$segment = array_shift($segments);
		
		/* start searching in the directory */
		foreach (new DirectoryIterator($dir) as $file) {
			if (!$file->isDot()) {
				$files = array_merge($files, $this->_search($file, $segment, $segments, $extension, $builder));
			}
		}
		
		if (!empty($orderBy)) {
			$this->_orderBy = $orderBy;
			usort($files, array($this, '_sortFiles'));
		}
		
		return $files;
	}
	
	/**
	 * Sort function for files
	 *
	 * @param string $file1
	 * @param string $file2
	 * @return bool
	 */
	protected function _sortFiles($file1, $file2)
	{
		return strnatcmp($file1->{$this->_orderBy}, $file2->{$this->_orderBy});
	}
	
	/**
	 * Searches for matching files
	 *
	 * @param Iterator $file
	 * @param string $segment
	 * @param array $segments
	 * @param string $extension
	 * @param Atomik_Model_Builder $builder
	 * @return array
	 */
	protected function _search($file, $segment, $segments, $extension, $builder)
	{
		$files = array();
		
		if (!$file->isDir()) {
			/* not a directory */
			
			$filename = $file->getFilename();
			
			if ($extension === null) {
				/* no need to check the extension, to match the segment can either be a variable
				 * or match the current filename */
				if (substr($segment, 0, 1) == ':' || $filename == $segment) {
					$files[] = $this->_createInstanceFromFile($file->getPathname(), $builder);
				}
			} else {
				/* checking the extension, extracting the extension from the filename */
				$fileExt = strtolower(substr($filename, strrpos($filename, '.') + 1));
				$filename = substr($filename, 0, strrpos($filename, '.'));
				/* to match, the segment can either be a variable or match the filename (without the extension)
				 * the file extension must matched the searched extensions */
				if ((substr($segment, 0, 1) == ':' || $filename == $segment) && $fileExt == $extension) {
					$files[] = $this->_createInstanceFromFile($file->getPathname(), $builder);
				}
			}
			
		} else {
			/* it is a directory*/
			
			/* the segment is not a variable and the filename does not match */
			if (substr($segment, 0, 1) != ':' && $file->getFilename() != $segment) {
				return array();
			}
			
			/* the segment is either a variable or it matches the current filename
			 * checking in sub files */
			$segment = array_shift($segments);
			foreach (new DirectoryIterator($file->getPathname()) as $subFile) {
				if (!$file->isDot()) {
					$files = array_merge($files, $this->_search($subFile, $segment, $segments, $extension, $builder));
				}
			}
		}
		
		return $files;
	}
	
	/**
	 * Creates a model instance from a filename
	 *
	 * @param string $filename
	 * @param Atomik_Model_Builder $builder
	 * @return Atomik_Model
	 */
	protected function _createInstanceFromFile($filename, $builder)
	{
		$dir = $this->_getDir($builder);
		
		/* gets the filename without the directory path */
		$filename = ltrim(substr($filename, strlen($dir)), '/');
		$fullname = $filename;
		
		/* removes the extension in the path and in the filename if present */
		$templateFilename = $builder->getOption('filename');
		if (($dot = strrpos($templateFilename, '.')) !== false) {
			$templateFilename = substr($templateFilename, 0, $dot);
			$filename = substr($filename, 0, strrpos($filename, '.'));
		}
		
		$fileSegments = explode('/', $filename);
		$segments = explode('/', $templateFilename);
		$values = array();
		
		/* searches in path segments for variables and matching them with the filename segments */
		for ($i = 0, $c = count($segments); $i < $c; $i++) {
			if (substr($segments[$i], 0, 1) == ':') {
				$values[substr($segments[$i], 1)] = $fileSegments[$i];
			}
		}
		
		foreach ($builder->getFields() as $field) {
			if ($field->hasOption('file-content')) {
				$values[$field->getName()] = file_get_contents($file->getPathname());
				
			} else if ($field->hasOption('filename')) {
				$values[$field->getName()] = $fullname;
			}
		}
		
		return $builder->createInstance($values);
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
		$file = $this->_getDir($builder) . $this->_getFilename($builder, $where);
		
		if (!file_exists($file)) {
			return null;
		}
		
		return $this->_createInstanceFromFile($file, $builder);
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
	
	/**
	 * Gets the directory where files are stored
	 *
	 * @param Atomik_Model_Builder $builder
	 * @return string
	 */
	protected function _getDir(Atomik_Model_Builder $builder)
	{
		if (($dir = $builder->getOption('dir', null)) === null) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Missing path key in ' . $builder->getName() . ' model');
		}
		
		/* making the dir absolute */
		if (substr($dir, 0, 1) != '/') {
			$dir = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $dir;
		}
		
		return rtrim($dir, '/') . '/';
	}
	
	/**
	 * Transforms the filename template of a file to a real filename
	 *
	 * @param Atomik_Model_Builder $builder
	 * @param array $data
	 * @return string
	 */
	protected function _getFilename(Atomik_Model_Builder $builder, $data)
	{
		if (($path = $builder->getOption('filename', null)) === null) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Missing path key in ' . $builder->getName() . ' model');
		}
		foreach ($data as $key => $value) {
			$path = str_replace(':' . $key, $value, $path);
		}
		return ltrim($path, '/');
	}
}