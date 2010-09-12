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

/**
 * Controller plugin
 * 
 * @package Atomik
 * @subpackage Plugins
 */
class ControllerPlugin
{
	/** @var array */
    public static $config = array();
    
    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start(&$config)
    {
        $config = array_merge(array(
        
        	// default action name
        	'default_action' => 'index',
        
            // directories where to find controllers
        	'controller_dirs' => ATOMIK_APP_ROOT . '/controllers',
        
            // whether a controller must exists 
            // (will trigger error even if a view exists)
            'controller_must_exists' => true,
        
            // default controller namespaces
        	'default_namespace' => '',
        
            // namespace separator
            'namespace_separator' => '_'
            
        ), $config);
        
    	self::$config = &$config;
        Atomik::set('app/executor', 'ControllerPlugin::execute');
		
		// adds controllers directories to php's include path
		$includes = explode(PATH_SEPARATOR, get_include_path());
		foreach (Atomik::path(self::$config['controller_dirs'], true) as $dir) {
			if (!in_array($dir, $includes)) {
				array_unshift($includes, $dir);
			}
		}
		set_include_path(implode(PATH_SEPARATOR, $includes));
    }
    
    /**
     * Returns a controller's filename
     *
     * @param string $controller
     * @return string
     */
    public static function controllerFilename($controller)
    {
        $filename = str_replace(' ', '/', ucwords(str_replace('/', ' ', $controller)))
                  . 'Controller.php';
                  
        return Atomik::path($filename, self::$config['controller_dirs']);
    }
	
	/**
	 * Executor which defines controllers and actions MVC-style
	 *
     * @param string $action
     * @param string $method
     * @param array  $context
     * @return array
	 */
    public static function execute($action, $method, $vars, &$context)
    {
	    $controller = trim(dirname($action), './');
	    $action = basename($action);
	    if (empty($controller)) {
	        $controller = $action;
	        $action = self::$config['default_action'];
	    }
	    
        $className = ltrim(self::$config['default_namespace'] . self::$config['namespace_separator'] 
                   . str_replace(' ', self::$config['namespace_separator'], ucwords(str_replace('/', ' ', $controller))) 
                   . 'Controller', '_');
	    
        if (($filename = self::controllerFilename($controller)) === false) {
            if (self::$config['controller_must_exists']) {
                throw new Atomik_HttpException("Class '$className' not found", 404);
            }
            return false;
        }
        
        Atomik::fireEvent('Controller::Execute', array(&$filename, &$className, &$context));
        
        require_once $filename;
        if (!class_exists($className)) {
            throw new Atomik_Exception("Class $className not found in $filename");
        } else if (!is_subclass_of($className, 'Atomik_Controller')) {
            throw new Atomik_Exception("Class $className must subclass Atomik_Controller");
        }
		
		$instance = new $className();
		if (($instance->_dispatch($action, $method, $vars)) === false) {
		    return false;
		}
		return get_object_vars($instance);
    }
}

