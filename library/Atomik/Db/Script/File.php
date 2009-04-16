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

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Script_File implements Atomik_Db_Script_Interface
{
	/**
	 * @var string
	 */
	public $filename;
	
	/**
	 * Returns an array of script obtained from a directory
	 * 
	 * @param	string	$dir
	 * @return 	array
	 */
	public static function getScriptFromDir($dir)
	{
		$scripts = array();
		
		foreach (new DirectoryIterator($dir) as $file) {
			if ($file->isDot() || substr($file->getFilename(), 0, 1) == '.') {
				continue;
			}
			
			if ($file->isDir()) {
				$scripts = array_merge($scripts, self::getScriptFromDir($file->getPathname()));
				continue;
			}
			
			$scripts[] = new Atomik_Db_Script_File($file->getPathname());
		}
		
		return $scripts;
	}
	
	/**
	 * Constructor
	 * 
	 * @param	string	$filename
	 */
	public function __construct($filename = null)
	{
		$this->filename = $filename;
	}
	
	/**
	 * Returns the files content
	 * 
	 * @return string
	 */
	public function getSql()
	{
		if (empty($this->filename)) {
			require_once 'Atomik/Db/Script/Exception.php';
			throw new Atomik_Db_Script_Exception('No file has been specified for Atomik_Db_Script_File');
		}
		return file_get_contents($this->filename);
	}
	
	/**
	 * @see Atomik_Db_Script_File::getSql()
	 */
	public function __toString()
	{
		return $this->getSql();
	}
}