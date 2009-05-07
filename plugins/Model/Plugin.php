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
 * @subpackage Plugins
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Model */
require_once 'Atomik/Model.php';
    	
/**
 * Helpers function for handling databases
 *
 * @package Atomik
 * @subpackage Plugins
 */
class ModelPlugin
{
	/**
	 * Default configuration
	 * 
	 * @var array 
	 */
    public static $config = array (
    
    	// directories where models are stored
    	'model_dirs' 			=> './app/models',
    
    	// default model adapter
    	'default_model_adapter' => 'Db'
    	
    );
    
    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start($config)
    {
    	self::$config = array_merge(self::$config, $config);
		
		// adds models directories to php's include path
		$includes = explode(PATH_SEPARATOR, get_include_path());
		foreach (Atomik::path(self::$config['model_dirs'], true) as $dir) {
			if (!in_array($dir, $includes)) {
				array_unshift($includes, $dir);
			}
		}
		set_include_path(implode(PATH_SEPARATOR, $includes));
		
		// loads the default model adapter
		if (!empty(self::$config['default_model_adapter'])) {
			require_once 'Atomik/Model/Adapter/Factory.php';
			$adapter = Atomik_Model_Adapter_Factory::factory(self::$config['default_model_adapter']);
			Atomik_Model_Builder::setDefaultAdapter($adapter);
		}
    }
    
    /**
     * Adds models folders to php's include path
     */
    public static function onAtomikStart()
    {
		$includes = explode(PATH_SEPARATOR, get_include_path());
		
		// add plugin's models folder to php's include path 
		foreach (Atomik::getLoadedPlugins(true) as $plugin => $dir) {
			if (!in_array($dir . '/models', $includes)) {
				array_unshift($includes, $dir . '/models');
			}
		}
		
		set_include_path(implode(PATH_SEPARATOR, $includes));
    }
    
    /**
     * Backend support
     * Adds tabs
     */
    public static function onBackendStart()
    {
    	Atomik_Backend::addMenu('models', 'Models', 'models', array(), 'right');
    }
    
    public static function onDbScript($script, $paths)
    {
		require_once 'Atomik/Db/Script/Model.php';
		
		foreach ($paths as $path) {
			if (@is_dir($path . '/models')) {
				$script->addScripts(Atomik_Db_Script_Model::getScriptFromDir($path . '/models'));
			}
		}
    }
}

