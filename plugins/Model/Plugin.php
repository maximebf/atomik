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
 * @subpackage Plugins
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * Model plugin
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
    public static $config = array(
    
    	/* directories where models are stored */
    	'dirs' => './app/models',
    
    	/* directories where adapters are stored */
    	'adapters_dirs' => array(
    		
    		'./app/adapters'
    
    	),
    
    	/* default model adapter */
    	'default_adapter' => 'Atomik_Model_Adapter_Local'
    	
    );
    
    /**
     * @param array $config
     * @return bool
     */
    public static function start($config)
    {
    	/* adds the default adapters dirs */
    	self::$config['adapters_dirs'][] = dirname(__FILE__) . '/adapters';
    	
        /* config */
        self::$config = array_merge(self::$config, $config);
		
		/* adds include path */
		$includes = array();
		foreach (Atomik::path(self::$config['dirs'], true) as $dir) {
			$includes[] = $dir;
		}
		$includes[] = get_include_path();
		set_include_path(implode(PATH_SEPARATOR, $includes));

		require_once 'Atomik/Model.php';
		
		/* loads the default adapter */
		if (!empty(self::$config['default_adapter'])) {
			$classname = self::$config['default_adapter'];
			Atomik_Model_Builder::setDefaultAdapter(new $classname());
		}
    }
    
    /**
     * Backend support
     * Adds tabs
     */
    public static function onBackendStart()
    {
    	Atomik_Backend::addTab('Models', 'Model', 'list', 'right');
    }
}