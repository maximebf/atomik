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
    
    	/* default controller name */
    	'default_controller' => 'index',
    
    	/* default action name */
    	'default_action' => 'index',
    
    );
    
    /**
     * @var array
     */
    private static $request;
    
    /**
     * @var bool
     */
    public static $disable = false;
    
    /**
     * Plugin starts
     *
     * @param array $config
     * @param bool $doNotRouteIfAlreadyDispatched OPTIONAL
     */
    public static function start($config, $doNotRouteIfAlreadyDispatched = false)
    {
        self::$config = array_merge(self::$config, $config);
        require_once 'Atomik/Controller.php';
    }
	
    /**
     * Disable the controller plugin for pluggable applications
     */
	public static function onAtomikDispatchpluginapplicationReady()
	{
		self::$disable = true;
	}
	
	/**
	 * Changes the action name for atomik to find the file
	 */
	public static function onAtomikExecuteStart(&$action, &$context, &$triggerError)
	{
		if (self::$disable) {
			return;
		}
		
	    self::$request = Atomik::getRef('request');
	    
	    if (!isset(self::$request['controller'])) {
	    	$segments = explode('/', self::$request['action']);
	    	
	    	self::$request['action'] = self::$config['default_action'];
	    	if (count($segments) > 1) {
	    		self::$request['action'] = array_pop($segments);
	    	}
	    	
	    	if (count($segments) == 0) {
	    		self::$request['controller'] = self::$config['default_controller'];
	    	} else {
	    		self::$request['controller'] = implode('/', $segments);
	    	}
	    	
	    } else if (!isset(self::$request['action'])) {
		    self::$request['action'] = self::$config['default_action'];
	    }
	    
	    /* overrides action name */
		$action = self::$request['controller'];
		
		/* overrides template's filename */
		$context['view'] = self::$request['controller'] . '/' . self::$request['action'];
	}
	
	/**
	 * Dispatch the request to the controller action
	 */
	public static function onAtomikExecuteAfter($action, &$context, &$vars, &$triggerError)
	{
		if (self::$disable) {
			return;
		}
		
		$request = self::$request;
		Atomik::fireEvent('Controller::Dispatch::Before', array(&$context, &$vars));
		
		/* controller class name */
		$classname = str_replace(' ', '_', ucwords(str_replace('/', ' ', $request['controller']))) . 'Controller';
		
		/* checks if the controller class exists */
		if (!class_exists($classname, false)) {
			throw new Exception('Controller ' . $classname . ' not found');
		}
		
		/* creates the controller instance */
		$instance = new $classname();
		
		Atomik::fireEvent('Controller::Action::Before', array(&$instance));
		
		if (!($instance instanceof Atomik_Controller)) {
			
			/* call the method named like the action with the request as unique argument */
			call_user_func(array($instance, $request['action']), $request);
		
			/* gets the instance properties and sets them in the global scope for the view */
			foreach (get_object_vars($instance) as $name => $value) {
				if (substr($name, 0, 1) != '_') {
					$vars[$name] = $value;
				}
			}
			
		} else {
			/* instance is of type Atomik_Controller */
			$vars = $instance->_dispatch($request);
		}
		
		Atomik::fireEvent('Controller::Dispatch::After', array($instance, &$context['view'], &$vars));
	}
	
	/**
	 * Overrides default generator behaviour
	 */
	public static function onConsoleGenerate($action)
	{
		ConsolePlugin::println('Generating controller structure');
		
		/* adds a class definition inside the action file */
		$filename = Atomik::path($action . '.php', Atomik::get('atomik/dirs/actions'));
		ConsolePlugin::touch($filename, "<?php\n\nclass " . ucfirst($action) . "Controller extends Atomik_Controller\n{\n}\n", 1);
		
		/* removes the presentation file and replaces it with a directory */
		$templatePath = Atomik::path(Atomik::get('atomik/dirs/views'));
		@unlink($templatePath . $action . '.php');
		ConsolePlugin::mkdir($templatePath . $action, 1);
	}
}

