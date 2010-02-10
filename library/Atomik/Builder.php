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
 * @subpackage Builder
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * Builds custom distribution
 * 
 * @package Atomik
 * @subpackage Builder
 */
class Atomik_Builder
{
	/**
	 * The path where the plugin will be built. Must be writeable.
	 * 
	 * @var string
	 */
	public $buildPath;
	
	/**
	 * The filename of the release archive
	 *  
	 * @var string
	 */
	public $releaseArchiveFilename = 'http://www.atomikframework.com/download/latest';
	
	/**
	 * Custom configuration
	 * 
	 * @var array
	 */
	public $config = array(
		'styles' => array('assets/css/main.css')
	);
	
	/**
	 * Plugins to include. The array keys must be the plugin's archive filename and 
	 * the value must be its Manifest.xml filename or an instance of Atomik_Manifest.
	 * 
	 * @var array
	 */
	public $plugins = array();
	
	/**
	 * Javascripts to include. Array keys must be the script filename and the value
	 * a boolean indicating whether to create a javascript file or only add the path
	 * in the config.
	 * 
	 * @var array
	 */
	public $javascripts = array();
	
	/**
	 * Whether to include a .htaccess file
	 * 
	 * @var bool
	 */
	public $useHtaccess = true;
	
	/**
	 * Constructor
	 * 
	 * @param string $buildPath Where to build the distribution
	 */
	public function __construct($buildPath = null)
	{
		if ($buildPath === null) {
			$buildPath = '/tmp/' . uniqid();
		}
		$this->buildPath = $buildPath;
	}
	
	/**
	 * Builds a zip archive of your custom distribution
	 * 
	 * @param	string	$zipFilename	The filename of the archive to create
	 * @return 	string 					The filename to the zip archive
	 */
	public function build($zipFilename = null)
	{
		$this->buildPath = rtrim($this->buildPath, '/');
		
		// extract the release to the build path
		$release = new ZipArchive();
		if ($release->open($this->releaseArchiveFilename) !== true) {
			throw new Atomik_Builder_Exception('Atomik release archive not found');
		}
		$release->extractTo($this->buildPath);
		$release->close();
		
		$buildPath = $this->buildPath . '/atomik';
		if ($zipFilename === null) {
			$zipFilename = $this->buildPath . '.zip';
		}
		
		$this->_buildPlugins($buildPath);
		$this->_buildJavascript($buildPath);
		$this->_buildConfig($buildPath);
		
		// create a zip from the build path
		$zip = new ZipArchive();
		if ($zip->open($zipFilename, ZipArchive::CREATE) !== true) {
			throw new Atomik_Builder_Exception('Could not write custom archive');
		}
		$this->_addDirectoryToZip($zip, $zipFilename, $buildPath, 'atomik/');
		$zip->close();
		
		$this->_deleteDir($this->buildPath);
		return $zipFilename;
	}
	
	/**
	 * Adds plugins to the distribution
	 * 
	 * @param 	string	$buildPath 	The path where the distribution is being built
	 */
	protected function _buildPlugins($buildPath)
	{
		$this->config['plugins'] = array();
		$tmpDir = realpath($buildPath) . '/tmp';
		
		foreach ($this->plugins as $pluginArchiveFilename => $manifest) {
			mkdir($tmpDir);
			
			if (!file_exists($pluginArchiveFilename)) {
				throw new Atomik_Builder_Exception('The archive does not exist for the plugin ' . $pluginArchiveFilename);
			}
			
			// extract the plugin archive
			$zip = new ZipArchive();
			if ($zip->open($pluginArchiveFilename) !== true) {
				throw new Atomik_Builder_Exception('Could not open the plugin archive');
			}
			$zip->extractTo($tmpDir);
			$zip->close();
			
			$directory = $manifest->directory;
			
			// manifest files are not bundle with distributions
			if (file_exists($tmpDir . '/Manifest.xml')) {
				unlink($tmpDir . '/Manifest.xml');
			}
			
			// checks the plugin directory
			$dir = '/' . ltrim(str_replace('\\', '/', $directory), '/');
			if (($dir = realpath($tmpDir . $dir)) === false) {
				throw new Atomik_Builder_Exception('The plugin\'s directory for ' . $pluginArchiveFilename . ' cannot be found');
			}
			if (substr($dir, 0, strlen($tmpDir)) != $tmpDir) {
				throw new Atomik_Builder_Exception('The plugin\'s directory value for ' . $pluginArchiveFilename . ' reference a location outside of the plugin archive');
			}
			
			// moving all files from temp dir to app/plugins
			foreach (new DirectoryIterator($tmpDir) as $file) {
				if (substr($file->getFilename(), 0, 1) == '.') {
					continue;
				}
				rename($file->getPathname(), $buildPath . '/app/plugins/' . $file->getFilename());
			}
			
			$this->config['plugins'][] = ucfirst($manifest->name);
			$this->_deleteDir($tmpDir);
		}
	}
	
	/**
	 * Adds javascript files to the distribution
	 * 
	 * @param 	string	$buildPath 	The path where the distribution is being built
	 */
	protected function _buildJavascript($buildPath)
	{
		if (!isset($this->config['scripts'])) {
			$this->config['scripts'] = array();
		} else if (!is_array($this->config['scripts'])) {
			$this->config['scripts'] = array($this->config['scripts']);
		}
		
		foreach ($this->javascripts as $filename => $includeScript) {
			
			if ($includeScript) {
				$realFilename = $filename;
				$filename = 'assets/js/libs/' . basename($filename);
				$filepath = $buildPath . '/' . $filename;
				mkdir(dirname($filepath));
				file_put_contents($filepath, file_get_contents($realFilename));
			}
			
			$this->config['scripts'][] = $filename;
		}
	}
	
	/**
	 * Adds the configuration to the distribution, reconfiguring necessary aspects
	 * 
	 * @param 	string	$buildPath 	The path where the distribution is being built
	 */
	protected function _buildConfig($buildPath)
	{
		$viewExtension = isset($this->config['app']['views']['file_extension']) ? $this->config['app']['views']['file_extension'] : '.phtml';
		$layout	= isset($this->config['app']['layout']) ? $this->config['app']['layout'] : '_default';
		$defaultAction = isset($this->config['app']['default_action']) ? $this->config['app']['default_action'] : 'index';
		
		// default action
		$defaultActionPath = $buildPath . '/app/views/index.phtml';
		$newDefaultActionPath = $buildPath . '/app/views/' . $defaultAction . $viewExtension;
		
		if ($defaultActionPath != $newDefaultActionPath) {
			rename($defaultActionPath, $newDefaultActionPath);
		}
		
		// layout
		$layoutPath = $buildPath . '/app/views/_layout.phtml';
		$newLayoutPath = $buildPath . '/app/views/' . $layout . $viewExtension;
		
		if (empty($layout)) {
			unlink($layoutPath);
		} else if ($layoutPath != $newLayoutPath) {
			rename($layoutPath, $newLayoutPath);
		}
		
		// htaccess
		if (!$this->useHtaccess) {
			unlink($buildPath . '/htaccess');
		}
		
		// bootstrap
		$bootstrap = "<?php\n\nAtomik::set(" . var_export($this->config, true) . ");\n";
		file_put_contents($buildPath . '/app/config.php', $bootstrap);
	}
	
	/**
	 * Adds recursively a directory to a ZipArchive object
	 * 
	 * @param	ZipArchive	$zip			The zip archive
	 * @param	string		$zipFilename	The archive filename
	 * @param	string		$directory		The path of the directory to add
	 * @param	string		$base			Where to add the directory in the archive (default is the root)
	 * @param	int			$fileCount		Number of files already added to the archive (internal)
	 */
	protected function _addDirectoryToZip(ZipArchive $zip, $zipFilename, $directory, $base = '', $fileCount = 0)
	{
		foreach (new DirectoryIterator($directory) as $file) {
			if ($file->isDir()) {
				if (substr($file->getFilename(), 0, 1) == '.') {
					continue;
				}
				$zip->addEmptyDir($base . $file->getFilename());
				$this->_addDirectoryToZip($zip, $zipFilename, $file->getPathname(), $base . $file->getFilename() . '/', $fileCount);
				
			} else {
				if ($fileCount >= 100) {
					// due to a bug in ZipArchive it is needed to close the archive regularly when too many files are added
					$zip->close();
					$zip->open($zipFilename, ZipArchive::CREATE);
					$fileCount = 0;
				}
				$zip->addFile($file->getPathname(), $base . $file->getFilename());
				$fileCount++;
			}
		}
	}
	
	/**
	 * Deletes a directory and its content
	 * 
	 * @param	string	$dir
	 */
	protected function _deleteDir($dir)
	{
		$iter = new DirectoryIterator($dir);
		foreach ($iter as $file) {
			if ($file->isDir()) {
				if ($file->isDot() || $file->getFilename() == '..') {
					continue;
				}
				$this->_deleteDir($file->getPathname());
			} else {
				unlink($file->getPathname());
			}
		}
		rmdir($dir);
	}
}