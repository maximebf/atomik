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

/**
 * Stores models as files
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Link_File implements Atomik_Model_Linkable
{
	/**
	 * Gets the directory where files are stored
	 *
	 * @param Atomik_Model_Builder $builder
	 * @return string
	 */
	public static function getDirectoryFromBuilder(Atomik_Model_Builder $builder)
	{
		if (($dir = $builder->getOption('dir', null)) === null) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Missing dir optiom in ' . $builder->getName() . ' model');
		}
		
		// making the dir absolute
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
	public static function getFilenameFromBuilder(Atomik_Model_Builder $builder, $data = array())
	{
		if (($filename = $builder->getOption('filename', null)) === null) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Missing filename option in ' . $builder->getName() . ' model');
		}
		
		foreach ($data as $key => $value) {
			$filename = str_replace(':' . $key, $value, $filename);
		}
		
		return ltrim($filename, '/');
	}
	
	/**
	 * Returns the filename of a model associated file
	 * 
	 * @param 	Atomik_Model	$model
	 * @return 	string
	 */
	public static function getFilenameFromModel(Atomik_Model $model)
	{
		$dir = self::getDirectoryFromBuilder($model->getBuilder());
		$filename = self::getFilenameFromBuilder($model->getBuilder(), $model->toArray());
		
		if (preg_match('/:[a-zA-Z0-9]+/', $filename)) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Some fields are missing for the filename to be complete in ' . get_class($model));
		}
		
		return $dir . $filename;
	}
	
	/**
	 * Query the adapter
	 * 
	 * @param	Atomik_Model_Query	$query
	 * @return 	Atomik_Model_Modelset
	 */
	public function query(Atomik_Model_Query $query)
	{
		$builder = $query->from;
		$dir = self::getDirectoryFromBuilder($builder);
		$filename = self::getFilenameFromBuilder($builder, $query->where);
		$files = array();
		
		// removes the extension from the path
		$extension = null;
		if (($dot = strrpos($filename, '.')) !== false) {
			$extension = strtolower(substr($filename, $dot + 1));
			$filename = substr($filename, 0, $dot);
		}
		
		$segments = explode('/', $filename);
		$segment = array_shift($segments);
		
		// start searching in the directory
		foreach (new DirectoryIterator($dir) as $file) {
			if (!$file->isDot()) {
				$files = array_merge($files, $this->_search($file, $segment, $segments, $extension, $builder));
			}
		}
		
		if (!empty($query->orderByField)) {
			$this->_orderBy = $query->orderByField;
			usort($files, array($this, '_sortFiles'));
		}
		
		return $files;
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
		
		if (!$file->isDir() && count($segments) == 0) {
			// not a directory
			
			$filename = $file->getFilename();
			$fileExt = null;
			
			if ($extension !== null) {
				// extracting the extension from the filename
				$fileExt = strtolower(substr($filename, strrpos($filename, '.') + 1));
				$filename = substr($filename, 0, strrpos($filename, '.'));
			}
			
			// to match, the segment can either be a variable or match the filename (without the extension)
			// the file extension must matched the searched extensions
			if ((substr($segment, 0, 1) == ':' || $filename == $segment) && $fileExt == $extension) {
				$files[] = $this->_getDataFromFile($file->getPathname(), $builder);
			}
			
		} else if ($file->isDir()) {
			// it is a directory
			
			// the segment is not a variable and the filename does not match
			if (substr($segment, 0, 1) != ':' && $file->getFilename() != $segment) {
				return array();
			}
			
			// the segment is either a variable or it matches the current filename
			// checking in sub files
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
	 * Creates a model instance from a filename
	 *
	 * @param string $filename
	 * @param Atomik_Model_Builder $builder
	 * @return Atomik_Model
	 */
	protected function _getDataFromFile($filename, $builder)
	{
		$dir = self::getDirectoryFromBuilder($builder);
		
		// gets the filename without the directory path
		$fullname = $filename;
		$filename = ltrim(substr($filename, strlen($dir)), '/');
		
		// removes the extension in the filename if present
		$templateFilename = $builder->getOption('filename');
		if (($dot = strrpos($templateFilename, '.')) !== false) {
			$templateFilename = substr($templateFilename, 0, $dot);
			$filename = substr($filename, 0, strrpos($filename, '.'));
		}
		
		$fileSegments = explode('/', $filename);
		$segments = explode('/', $templateFilename);
		$data = array();
		
		// searches in path segments for variables and matching them with the filename segments
		for ($i = 0, $c = count($segments); $i < $c; $i++) {
			if (substr($segments[$i], 0, 1) == ':') {
				$data[substr($segments[$i], 1)] = $fileSegments[$i];
			}
		}
		
		foreach ($builder->getFields() as $field) {
			if ($field->hasOption('file-content')) {
				$data[$field->name] = file_get_contents($fullname);
				
			} else if ($field->hasOption('filename')) {
				$data[$field->name] = $fullname;
			}
		}
		
		return $data;
	}
}