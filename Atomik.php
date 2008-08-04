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
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */
	 
define('ATOMIK_VERSION', '2.1');

/* -------------------------------------------------------------------------------------------
 *  DEFAULT CONFIGURATION
 * ------------------------------------------------------------------------------------------ */

Atomik::set(array(

	/* plugins */
	'plugins'				    => array(),

    'atomik' => array(
    
    	/* request */
    	'trigger' 			    => 'action',
    	'default_action' 		=> 'index',

		/* register the class autoloader */
		'class_autoload'		=> true,
    
    	/* dirs */
        'dirs' => array(
        	'plugins'			=> './app/plugins/',
        	'actions' 			=> './app/actions/',
        	'templates'	 		=> './app/templates/',
        	'includes'			=> array('./app/includes/', './app/libraries')
        ),
    
    	/* files */
        'files' => array(
        	'config' 		    => './app/config.php',
        	'pre_dispatch' 	    => './app/pre_dispatch.php',
        	'post_dispatch' 	=> './app/post_dispatch.php',
        	'404' 			    => './app/404.php',
        	'error' 			=> './app/error.php'
        ),
    	
    	/* error management */
    	'catch_errors'			=> false,
    	'display_errors'		=> true,
        'error_report_attrs'	=> array(
		    'atomik-error'               => 'style="padding: 10px"',
		    'atomik-error-title'    	 => 'style="font-size: 1.3em; font-weight: bold; color: #FF0000"',
		    'atomik-error-lines'         => 'style="width: 100%; margin-bottom: 20px; background-color: #fff;'
		                                  . 'border: 1px solid #000; font-size: 0.8em"',
		    'atomik-error-line'          => '',
		    'atomik-error-line-error'    => 'style="background-color: #ffe8e7"',
		    'atomik-error-line-number'   => 'style="background-color: #eeeeee"',
		    'atomik-error-line-text'	 => '',
		    'atomik-error-stack'		 => ''
		)
    	
    ),
    
    'start_time' 				=> time() + microtime()
));

/* creates the A function (shortcut to Atomik::get) */
if (!function_exists('A')) {
    /**
     * Shortcut function to Atomik::get()
     * Useful when dealing with selectors
     *
     * @see Atomik::get()
     * @return mixed
     */
    function A()
    {
        $args = func_get_args();
        return call_user_func_array(array('Atomik', 'get'), $args);
    }
}

/* starts Atomik unless ATOMIK_AUTORUN is set to false */
if (!defined('ATOMIK_AUTORUN') || ATOMIK_AUTORUN === true) {
    Atomik::dispatch();
}

/**
 * Atomik Framework Main class
 *
 * @package Atomik
 */
class Atomik
{
    /**
     * Global store
     *
     * @var array
     */
	protected static $_store = array();
	
	/**
	 * Loaded plugins
	 *
	 * @var array
	 */
	protected static $_plugins = array();
	
	/**
	 * Registered events
	 *
	 * @var array
	 */
	protected static $_events = array();
	
	/**
	 * Selectors namespaces
	 *
	 * @var array
	 */
	protected static $_namespaces = array();
	
	/**
	 * Dispatches the request
	 * 
	 * @param string $request OPTIONAL
	 */
	public static function dispatch($request = null)
	{
	    /* wrap the whole app inside a try/catch block to catch all erros */
	    try {
    		 
    		/* loads external configuration */
    		if (file_exists($filename = self::get('atomik/files/config'))) {
    			require($filename);
    		}
    		
    		/* adds includes dirs to php include path */
    		$includePath = '';
    		foreach (self::path(self::get('atomik/dirs/includes'), true) as $dir) {
    		    if (@is_dir($dir)) {
    		        $includePath .= PATH_SEPARATOR . $dir;
    		    }
    		}
    		set_include_path(get_include_path() . $includePath);
	        
    		/* registers the error handler */
    		if (self::get('atomik/catch_errors', true) === true) {
    			set_error_handler(array('Atomik', 'errorHandler'));
    		}
    		
    		/* registers the class autoload handler */
    		if (self::get('atomik/class_autoload', true) === true) {
    			if (!function_exists('spl_autoload_register')) {
    				throw new Exception('Missing spl_autoload_register function');
    			}
    			spl_autoload_register(array('Atomik', 'needed'));
    		}
    	
    		/* loads plugins */
    		foreach (self::get('plugins') as $key => $value) {
    		    if (!is_string($key)) {
    		        $key = $value;
    		        $value = array();
    		    }
    			self::loadPlugin($key, $value);
    		}
    	
    		/* core is starting */
    		self::fireEvent('Atomik::Start');
    	
    		/* checks if url rewriting is used */
    		if (!self::has('url_rewriting')) {
    			self::set('url_rewriting', isset($_SERVER['REDIRECT_URL']));
    		}
    	
    		/* checks if it's needed to auto discover the request */
    		if ($request === null) {
    		    
        		/* retreives the requested url */
        		$trigger = self::get('atomik/trigger');
        		if (!isset($_GET[$trigger]) || empty($_GET[$trigger])) {
        		    
        			/* no trigger specified, using default page name */
        			self::set('request', self::get('atomik/default_action'));
        			
        		} else {
        		    
        		    $request = trim($_GET[$trigger], '/');
        		
        			/* checking if no dot are in the page name to avoid any hack attempt and if no 
        			 * underscore is use as first character in a segment */
        			if (strpos($request, '..') !== false || substr($request, 0, 1) == '_' 
        			    || strpos($request, '/_') !== false) {
        				    self::trigger404();
        			}
        			
        			self::set('request', $request);
        		}
    	
        		/* retreives the base url */
        		if (!self::has('base_url')) {
        			if (self::get('url_rewriting') && isset($_SERVER['REDIRECT_URL'])) {
        			    /* finds the base url from the redirected url */
        				self::set('base_url', substr($_SERVER['REDIRECT_URL'], 0, -strlen($_GET[$trigger])));
        			} else {
        			    /* finds the base url from the script name */
        				self::set('base_url', dirname($_SERVER['SCRIPT_NAME']) . '/');
        			}
        		}
        		
    		} else {
    		    /* sets the user defined request */
    		    self::set('request', $request);
            	/* retreives the base url */
            	if (!self::has('base_url')) {
    			    /* finds the base url from the script name */
    				self::set('base_url', dirname($_SERVER['SCRIPT_NAME']) . '/');
            	}
    		}
    	
    		/* all configuration has been set, ready to dispatch */
    		self::fireEvent('Atomik::Dispatch::Before');
    	
    		/* global pre dispatch action */
    		if (file_exists($filename = self::get('atomik/files/pre_dispatch'))) {
    			require($filename);
    		}
    	
    		/* executes the action */
    		if (self::execute(self::get('request'), true, true, false) === false) {
    			self::trigger404();
    		}
    	
    		/* dispatch done */
    		self::fireEvent('Atomik::Dispatch::After');
    	
    		/* global post dispatch action */
    		if (file_exists($filename = self::get('atomik/files/post_dispatch'))) {
    			require($filename);
    		}
    	
    		/* end */
    		self::end(true);
    		
	    } catch (Exception $e) {
	        
			/* checks if we really want to catch errors */
	        if (!self::get('atomik/catch_errors', true)) {
	            throw $e;
	        }
	        
			self::fireEvent('Atomik::Error', array($e));
			self::renderException($e);
			self::end(false);
	    }
	}
	
	/**
	 * Executes an action
	 *
	 * @see Atomik::render()
	 * @param string $action
	 * @param bool $render OPTIONAL (default true)
	 * @param bool $echo OPTIONAL (default false)
	 * @param bool $triggerError OPTIONAL (default true)
	 * @return array|string|bool
	 */
	public static function execute($action, $render = true, $echo = false, $triggerError = true)
	{
		$template = $action;
		$vars = array();
	
		self::fireEvent('Atomik::Execute::Start', array(&$action, &$template, &$render, &$echo, &$triggerError));
		
		$actionFilename = self::path($action . '.php', self::get('atomik/dirs/actions'));
		$templateFilename = self::path($template . '.php', self::get('atomik/dirs/templates'));
		
		/* checks if at least the action file or the template file is defined */
		if ($actionFilename === false && $templateFilename === false) {
			if ($triggerError) {
				throw new Exception('Action ' . $action . ' does not exists');
			}
			return false;
		}
	
		self::fireEvent('Atomik::Execute::Before', array(&$action, &$template, &$render, &$echo, &$triggerError));
	
		/* executes the action */
		if ($actionFilename !== false) {
		    /* executes the action in its own scope and fetches defined variables */
			$vars = self::_executeInScope($actionFilename);
		}
	
		self::fireEvent('Atomik::Execute::After', array($action, &$template, &$vars, &$render, &$echo, &$triggerError));
		
		/* returns $vars if the template is not rendered */
		if (!$render || $templateFilename === false) {
			return $vars;
		}
		
		/* renders the template associated to the action */
		return self::render($template, $vars, $echo, $triggerError);
	}
	
	/**
	 * Requires the actions file inside a clean scope and returns defined
	 * variables
	 *
	 * @param string $__action_filename
	 * @return array
	 */
	public static function _executeInScope($__action_filename)
	{
		require($__action_filename);
		
		/* retreives "public" variables (not prefixed with an underscore) */
		$definedVars = get_defined_vars();
		$vars = array();
		foreach ($definedVars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$vars[$name] = $value;
			}
		}
		
		return $vars;
	}
	
	/**
	 * Renders a template
	 *
	 * @param string $template
	 * @param array $vars OPTIONAL
	 * @param bool $echo OPTIONAL (default false)
	 * @param bool $triggerError OPTIONAL (default true)
	 * @return string|bool
	 */
	public static function render($template, $vars = array(), $echo = false, $triggerError = true)
	{
		/* template filename */
		$filename = self::path($template . '.php', self::get('atomik/dirs/templates'));
		
		self::fireEvent('Atomik::Render::Start', array(&$template, &$vars, &$echo, &$triggerError, &$filename));
		
		/* checks if the file exists */
		if ($filename === false) {
			if ($triggerError) {
				throw new Exception('Template ' . $filename . ' not found');
			}
			return false;
		}
		
		self::fireEvent('Atomik::Render::Before', array(&$template, &$vars, &$echo, &$triggerError, &$filename));
		
		/* render the template in its own scope */
		$output = self::_renderInScope($filename, $vars);
		
		self::fireEvent('Atomik::Render::After', array($template, &$output, &$vars, &$filename, &$echo, &$triggerError));
		
		/* checks if it's needed to echo the output */
		if (!$echo) {
			return $output;
		}
		
		self::fireEvent('Atomik::Output::Before', array($template, &$output));
		
		/* echo output */
		echo $output;
		
		self::fireEvent('Atomik::Output::After', array($template, $output));
	}
	
	/**
	 * Renders a template in its own scope
	 *
	 * @param string $filename
	 * @param array $vars OPTIONAL
	 * @return string
	 */
	public static function _renderInScope($__template_filename, $vars = array())
	{
		extract($vars);
		ob_start();
		include($__template_filename);
		return ob_get_clean();
	}

	/**
	 * Fires the Atomik::End event and exits the application
	 *
	 * @param bool $success OPTIONAL
	 */
	public static function end($success = false)
	{
		self::fireEvent('Atomik::End', array($success));
		exit;
	}
	
	
	/* -------------------------------------------------------------------------------------------
	 *  Accessor methods
	 * ------------------------------------------------------------------------------------------ */
	
	
	/**
	 * Sets a key/value pair in the store
	 * 
	 * If the first argument is an array, values are merged recursively.
	 * 
	 * You can set values from sub arrays by using a path like key.
	 * For example, to set the value inside the array $array[key1][key2]
	 * use the key 'key1/key2'
	 * 
	 * Can be used on any array by specifying the third argument
	 *
	 * @param array|string $key Can be an array to set many key/value
	 * @param mixed $value OPTIONAL
	 * @param array $array OPTIONAL
	 * @param array $keys OPTIONAL INTERNAL
	 */
	public static function set($key, $value = null, &$array = null, $keys = null)
	{
		/* if $data is null, uses the global store */
		if ($array === null) {
		    $array = &self::$_store;
		}
	    
	    /* checks if the key is an array */
	    if (is_array($key)) {
		    /* merges the store and the array */
	        $array = self::_mergeRecursive($array, $key);
		    return;
	    }
	    
	    /* if $keys is null, it means that we have not recurse yet */
	    if ($keys === null) {
	        if (strpos($key, '/') === false) {
	            /* simple key */
	            self::$_store[$key] = $value;
	            return;
	        }
	        /* the key is a path */
	        $keys = explode('/', $key);
	    }
	    
	    /* gets the next key in the path */
	    $key = array_shift($keys);
	    if (count($keys) == 0) {
	        /* end of path, sets the value */
	        $array[$key] = $value;
	        return;
	    }
	    
	    /* creates the array for the key if it doesn't exists */
	    if (!array_key_exists($key, $array)) {
	        $array[$key] = array();
	    }
	    
	    /* goto the next segment */
	    self::set(null, $value, $array[$key], $keys);
	}
	
	/**
	 * Like array_merge() but recursively
	 *
	 * @see array_merge()
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 */
	protected static function _mergeRecursive($array1, $array2)
	{
	    $array = $array1;
	    foreach ($array2 as $key => $value) {
	        if (is_array($value) && array_key_exists($key, $array1) && is_array($array1[$key])) {
	            $array[$key] = self::_mergeRecursive($array1[$key], $value);
	            continue;
	        }
	        $array[$key] = $value;
	    }
	    return $array;
	}
	
	/**
	 * Gets a value using its associatied key from the store
	 * 
	 * You can fetch value from sub arrays by using a path like
	 * key. Separate each key with a slash. For example if you want 
	 * to fetch the value from an $store[key1][key2][key3] you can use
	 * key1/key2/key3
	 * 
	 * Can be used on any array by specifying the third argument
	 *
	 * @param string|array $key OPTIONAL If null, fetches all values (default null)
	 * @param mixed $default OPTIONAL Default value if the key is not found (default null)
	 * @param array $array OPTIONAL
	 * @return mixed
	 */
	public static function get($key = null, $default = null, $array = null)
	{
	    /* returns the store */
	    if ($key === null) {
	        return self::$_store;
	    }
	    
	    /* checks if a namespace is used */
	    if (is_string($key) && preg_match('/^([a-z]+):(.*)/', $key, $match)) {
	        /* checks if the namespace exists */
	        if (isset(self::$_namespaces[$match[1]])) {
	            /* calls the namespace callback and returns */
	            $args = func_get_args();
	            $args[0] = $match[2];
	            return call_user_func_array(self::$_namespaces[$match[1]], $args);
	        }
	    }
	    
		/* if $data is null, uses the global store */
		if ($array === null) {
		    $array = self::$_store;
		}
		
		/* checks if the $key is an array */
	    if (!is_array($key)) {
	        /* checks if it has slashes */
	        if (strpos($key, '/') === false) {
		        /* return the value */
	            return array_key_exists($key, $array) ? $array[$key] : $default;
	        }
	        
            /* creates an array by spliting using slashes */
            $key = explode('/', $key);
		}
		
		/* checks if the key exists */
		$firstKey = array_shift($key);
		if (isset($array[$firstKey])) {
		    if (count($key) > 0) {
		        /* there's still keys so it goes deeper */
		        return self::get($key, $default, $array[$firstKey]);
		    } else {
		        /* the key has been found */
		        return $array[$firstKey];
		    }
		}
		
		/* key not found, returns default */
		return $default;
	}
	
	/**
	 * Checks if a key is defined in the store
	 * Can check through sub array using a path like key
	 * @see Atomik::get()
	 * 
	 * Can be used on any array by specifying the second argument
	 *
	 * @param string $key
	 * @param array $array OPTIONAL
	 * @return bool
	 */
	public static function has($key, $array = null)
	{
	    /* returns the store */
	    if ($array === null) {
	        $array = self::$_store;
	    }
	    
		/* checks if the $key is an array */
	    if (!is_array($key)) {
	        /* checks if it has slashes */
    	    if (!strpos($key, '/')) {
    	        return array_key_exists($key, $array);
    	    }
            /* creates an array by spliting using slashes */
            $key = explode('/', $key);
	    }
	    
		/* checks if the key exists */
	    $firstKey = array_shift($key);
	    if (array_key_exists($firstKey, $array)) {
	        if (count($key) == 0) {
		        /* the key has been found */
	            return true;
	        } else if (is_array($array[$firstKey])) {
		        /* there's still keys so it goes deeper */
	            return self::has($key, $array[$firstKey]);
	        }
	    }
	    
		/* key not found */
	    return false;
	}
	
	/**
	 * Deletes a key from the store
	 * Can delete through sub array using a path like key
	 * @see Atomik::get()
	 * 
	 * Can be used on any array by specifying the second argument
	 *
	 * @param string $key
	 * @param array $array OPTIONAL
	 */
	public static function delete($key, &$array = null)
	{
	    /* returns the store */
	    if ($array === null) {
	        $array = &self::$_store;
	    }
	    
		/* checks if the $key is an array */
	    if (!is_array($key)) {
	        /* checks if it has slashes */
    	    if (!strpos($key, '/')) {
    	        if (array_key_exists($key, $array)) {
    	            unset($array[$key]);
    	            return;
    	        }
    	    }
            /* creates an array by spliting using slashes */
            $key = explode('/', $key);
	    }
	    
		/* checks if the key exists */
	    $firstKey = array_shift($key);
	    if (array_key_exists($firstKey, $array)) {
	        if (count($key) == 0) {
		        unset($array[$firstKey]);
	        } else if (is_array($array[$firstKey])) {
		        /* there's still keys so it goes deeper */
	            self::delete($key, $array[$firstKey]);
	        } else {
	            throw new Exception('Key "' . $key . '" does not exists');
	        }
	    }
	}
	
	/**
	 * Registers a new selector namespace
	 *
	 * @param string $namespace
	 * @param callback $callback
	 */
	public static function registerSelector($namespace, $callback)
	{
	    self::$_namespaces[$namespace] = $callback;
	}
	
	
	/* -------------------------------------------------------------------------------------------
	 *  Plugins methods
	 * ------------------------------------------------------------------------------------------ */
	
	
	/**
	 * Loads a plugin
	 *
	 * @param string $plugin
	 * @param array $config OPTIONAL Configuration for this backend
	 * @param array $arguments OPTIONAL Arguments to pass to the plugin
	 * @return bool Success
	 */
	public static function loadPlugin($plugin, $config = array(), $arguments = array())
	{
		$plugin = ucfirst($plugin);
		
		/* checks if the plugin is already loaded */
		if (in_array($plugin, self::$_plugins)) {
			return true;
		}
		
		self::fireEvent('Atomik::Plugin::Before', array(&$plugin, &$config, &$arguments));
		
		/* checks if $plugin has been set to false from one of the event callbacks */
		if ($plugin === false) {
			return false;
		}
		
		/* checks if the *Plugin class is defined */
		$pluginClass = $plugin . 'Plugin';
		if (!class_exists($pluginClass, false)) {
			
			/* tries to load the plugin from a file */
			if (($filename = self::path($plugin . '.php', self::get('atomik/dirs/plugins'))) === false) {
				/* no file, checks for a directory */
				if (($dirname = self::path($plugin, self::get('atomik/dirs/plugins'))) === false) {
					/* plugin not found */
					throw new Exception('Missing plugin (no file or no directory matching plugin name): ' . $plugin);
				} else {
					/* directory found, plugin file should be inside */
					$filename = $dirname . '/' . $plugin . '.php';
					if (!file_exists($filename)) {
						throw new Exception('Missing plugin (no file inside the plugin\'s directory): ' . $plugin);
					}
					/* adds the libraries folder from the plugin directory to the include path */
					if (@is_dir($dirname . '/libraries')) {
						set_include_path($dirname . '/libraries'. PATH_SEPARATOR . get_include_path());
					}
				}
			}
			
			/* loads the plugin */
			require($filename);
		}
		
		/* checks if the *Plugin class is defined. The use of this class
		 * is not mandatory in plugin file */
		if (class_exists($pluginClass, false)) {
		    $registerEventsCallback = true;
		    
			/* call the start method on the plugin class if it's defined */
		    if (method_exists($pluginClass, 'start')) {
		        array_unshift($arguments, $config);
    		    if (call_user_func_array(array($pluginClass, 'start'), $arguments) === false) {
    		        $registerEventsCallback = false;
    		    }
		    }
		    
		    /* automatically registers events callback for methods starting with "on" */
		    if ($registerEventsCallback) {
    		    $methods = get_class_methods($pluginClass);
    		    foreach ($methods as $method) {
    		        if (preg_match('/^on[A-Z].*$/', $method)) {
    		            $event = preg_replace('/(?<=\\w)([A-Z])/', '::\1', substr($method, 2));
    		            self::listenEvent($event, array($pluginClass, $method));
    		        }
    		    }
		    }
		}
		
		self::fireEvent('Atomik::Plugin::After', array($plugin));
		
		/* stores the plugin name so we won't load it twice */
		self::$_plugins[] = $plugin;
		return true;
	}
	
	/**
	 * Checks if a plugin is already loaded
	 *
	 * @param string $plugin
	 * @return bool
	 */
	public static function isPluginLoaded($plugin)
	{
		return in_array($plugin, self::$_plugins);
	}
	
	
	/* -------------------------------------------------------------------------------------------
	 *  Events methods
	 * ------------------------------------------------------------------------------------------ */
	
	
	/**
	 * Registers a callback to an event
	 *
	 * @param string $event
	 * @param callback $callback
	 * @param int $priority OPTIONAL
	 * @param bool $important OPTIONAL If a listener of the same priority already exists, register the new listener before the existing one. 
	 */
	public static function listenEvent($event, $callback, $priority = 50, $important = false)
	{
		/* initialize the current event array */
		if (!isset(self::$_events[$event])) {
			self::$_events[$event] = array();
		}
		
		while (isset(self::$_events[$event][$priority])) {
			$priority += $important ? -1 : 1;
		}
		
		/* stores the callback */
		self::$_events[$event][$priority] = $callback;
	}
	
	/**
	 * Fires an event
	 * 
	 * @param string $event
	 * @param array $args OPTIONAL Arguments for the callback
	 */
	public static function fireEvent($event, $args = array())
	{
	    if (isset(self::$_events[$event])) {
			foreach (self::$_events[$event] as $callback) {
				call_user_func_array($callback, $args);
			}
		}
	}
	
	
	/* -------------------------------------------------------------------------------------------
	 *  Helper methods
	 * ------------------------------------------------------------------------------------------ */
	
	/**
	 * Builds a path.
	 * 
	 * Multiple cases possible:
	 * 
	 *  1) path($setOfPaths, $asArray = false)
	 *     Returns a path from a set of paths (the set can be a string
	 *     or an array). If the second argument is true, it returns
	 *     all paths from the set as an array
	 *
	 *  2) path($file, $setOfPaths, $check = true)
	 *     Searches for a file in the set of paths and returns the
	 *     first one it finds. If $check is set to false, it returns
	 *     the filename as if it was in the first path from the set of
	 *     paths. Returns false when $check is true and no file where found.
	 *
	 * @param string|array $file
	 * @param string|array|bool $paths
	 * @param bool $check
	 * @return string|array
	 */
	public static function path($file, $paths = null, $check = true)
	{
	    /* case1, $file is an array */
	    if (is_array($file)) {
	        if ($paths === true) {
	            /* returns $paths as array */
	            return $file;
	        }
	        /* returns the first path from the paths */
	        return $file[0];
	    }
	    /* $file is a string */
        
	    /* case 1 */
        if ($paths === null || is_bool($paths)) {
            if ($paths === true) {
                /* returns $file as array */
                return array($file);
            }
            /* returns $file as string */
            return $file;
        }
        
        /* case 2, $paths is a string */
        if (is_string($paths)) {
            $filename = rtrim($paths, '/') . '/' . $file;
            if (!$check || file_exists($filename)) {
                return $filename;
            }
            return false;
        }
        
        /* case 2, $paths is an array */
        if (is_array($paths)) {
            foreach ($paths as $path) {
                $filename = rtrim($path, '/') . '/' . $file;
                if (!$check || file_exists($filename)) {
                    return $filename;
                }
            }
        }
        
        /* nothing found */
        return false;
	}
	
	/**
	 * Returns an url for the action depending on whether url rewriting
	 * is used or not
	 *
	 * @param string $action
	 * @param array $params OPTIONAL GET parameters
	 * @param bool $useIndex OPTIONAL (default true) Whether to use index.php in the url
	 * @return string
	 */
	public static function url($action = null, $params = array(), $useIndex = true)
	{
		if ($action === null) {
			$action = self::get('request');
		}
		
		/* base url */
		$url = rtrim(self::get('base_url', '.'), '/') . '/';
		
		/* removes the query string from the action */
		$queryString = '';
		if (strpos($action, '?') !== false) {
			$parts = explode('?', $action);
			$action = $parts[0];
			$queryString = $parts[1];
		}
		
		/* adds parameters to the query string */
		foreach ($params as $param => $value) {
			$queryString .= $param . '=' . urlencode($value);
		}
		
		/* checks if url rewriting is used */
		if (!$useIndex || self::get('url_rewriting', false) === true) {
			$url .= $action . (!empty($queryString) ? '?' . $queryString : '');
		} else {
			/* no url rewriting, using index.php */
			$url .= 'index.php?' . self::get('atomik/trigger') 
			      . '=' . $action . '&' . $queryString;
		}
		
		/* trigger an event */
		$args = func_get_args();
		unset($args[0]);	
		self::fireEvent('Atomik::Url', array($action, &$url, $args));
		
		return $url;
	}
	
	/**
	 * Triggers a 404 error
	 */
	public static function trigger404()
	{
		self::fireEvent('Atomik::404');
		
		/* HTTP header */
		header('HTTP/1.0 404 Not Found');
		
		if (file_exists($filename = self::get('atomik/files/404'))) {
			/* includes the 404 error file */
			include($filename);
		} else {
			echo '<h1>404 - File not found</h1>';
		}
		
		self::end();
	}

	/*
	 * Includes a file
	 *
	 * @param string $include Filename or class name following the PEAR convention
	 * @param bool $className OPTIONAL If false, $include can't be a class name
	 * @param string|array $dirs OPTIONAL Include from specific directories rather than include path
	 */
	public static function needed($include, $className = true, $dirs = null)
	{
		self::fireEvent('Atomik::Needed', array(&$include, &$className, &$dirs));
		if ($include === null) {
			return;
		}
		
		if ($className && strpos($include, '_') !== false) {
			$include = str_replace('_', DIRECTORY_SEPARATOR, $include);
		}
		$include .= '.php';
		
		if ($dirs !== null) {
	    	require_once(self::path($include, $dirs));
		} else {
			require_once($include);
		}
	}

	/**
	 * Redirects to another url
	 *
	 * @see Atomik::url()
	 * @param string $url
	 * @param bool $useUrl OPTIONAL (default true) Use Atomik::url()
	 */
	public static function redirect($url, $useUrl = true)
	{
		/* uses Atomik::url() */
		if ($useUrl) {
			$url = self::url($url);
		}
		
		self::fireEvent('Atomik::Redirect', array(&$url));
		
		/* redirects */
		header('Location: ' . $url);
		self::end();
	}
	
	/**
	 * Catch errors and throw ErrorException
	 *
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 * @param mixed $errcontext
	 */
	public static function errorHandler($errno, $errstr, $errfile = '', $errline = 0, $errcontext = null)
	{
		/* handles errors depending on the level defined with error_reporting */
		if ($errno <= error_reporting()) {
		    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		}
	}
	
	/**
	 * Renders an exception
	 * 
	 * @param Exception $exception
	 * @param bool $return OPTIONAL Return the output instead of printing it
	 * @return string
	 */
	public static function renderException($exception, $return = false)
	{	
		/* checks if the user defined error file is available */
		if (file_exists($filename = self::get('atomik/files/error'))) {
			include($filename);
			self::end(false);
		}
		
		$attributes = self::get('atomik/error_report_attrs');
	
		echo '<div ' . $attributes['atomik-error'] . '>'
		   . '<span ' . $attributes['atomik-error-title'] . '>'
		   . 'An error has occured!</span>';
		
		/* only display error information if atomik/display_errors is true */
		if (self::get('atomik/display_errors', false) === false) {
		    echo '</div>';
		    self::end(false);
		}
		
		/* builds the html erro report */
		$html = '<br />An error of type <strong>' . get_class($e) . '</strong> '
		      . 'was caught at <strong>line ' . $e->getLine() . '</strong><br />'
		      . 'in file <strong>' . $e->getFile() . '</strong>'
		      . '<p>' . $e->getMessage() . '</p>'
			  . '<table ' . $attributes['atomik-error-lines'] . '>';
		
	    /* builds the table which display the lines around the error */
		$lines = file($e->getFile());
		$start = $e->getLine() - 7;
		$start = $start < 0 ? 0 : $start;
		$end = $e->getLine() + 7;
		$end = $end > count($lines) ? count($lines) : $end; 
		for($i = $start; $i < $end; $i++) {
		    /* color the line with the error. with standard Exception, lines are */
			if($i == $e->getLine() - (get_class($e) != 'ErrorException' ? 1 : 0)) {
				$html .= '<tr ' . $attributes['atomik-error-line-error'] . '><td>';
			}
			else {
				$html .= '<tr ' . $attributes['atomik-error-line'] . '>'
				       . '<td ' . $attributes['atomik-error-line-number'] . '>';
			}
			$html .= $i . '</td><td ' . $attributes['atomik-error-line-text'] . '>' 
			       . (isset($lines[$i]) ? htmlspecialchars($lines[$i]) : '') . '</td></tr>';
		}
		
		$html .= '</table>'
		       . '<strong>Stack:</strong><p ' . $attributes['atomik-error-stack'] . '>' 
		       . nl2br($e->getTraceAsString())
		       . '</p></div>';
		
		echo $html;
	}
}
