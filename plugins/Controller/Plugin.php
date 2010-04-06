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
	/**
	 * Default configuration
	 * 
	 * @var array 
	 */
    public static $config = array(
    
    	// default action name
    	'default_action' => 'index',
    	'controller_dirs' => './app/controllers'
    
    );
    
    /**
     * @var array
     */
    private static $request;
    
    /**
     * Plugin starts
     *
     * @param array $config
     * @param bool $doNotRouteIfAlreadyDispatched OPTIONAL
     */
    public static function start($config, $doNotRouteIfAlreadyDispatched = false)
    {
        self::$config = array_merge(self::$config, $config);
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
        
        if (($filename = self::controllerFilename($controller)) === false) {
            return false;
        }
        $className = str_replace(' ', '_', ucwords(str_replace('/', ' ', $controller))) . 'Controller';
        
        Atomik::fireEvent('Controller::Execute', array(&$filename, &$className, &$context));
        
        require_once $filename;
        if (!class_exists($className)) {
            throw new Atomik_Exception("Class $className not found in $filename");
        } else if (!is_subclass_of($className, 'Atomik_Controller')) {
            throw new Atomik_Exception("Class $className must subclass Atomik_Controller");
        }
		
		$instance = new $className();
		return $instance->dispatch($action, $method, $vars);
    }
}

