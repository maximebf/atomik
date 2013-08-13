<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define('ATOMIK_VERSION', '3.1');


/* -------------------------------------------------------------------------------------------
 *  CORE
 * ------------------------------------------------------------------------------------------ */


/**
 * Exception class for Atomik
 * 
 * @package Atomik
 */
class AtomikException extends Exception {}


/**
 * HTTP Exception class for Atomik
 * 
 * The code must be an HTTP response code
 * 
 * @package Atomik
 */
class AtomikHttpException extends AtomikException {}


/**
 * Atomik Framework Main class
 *
 * @package Atomik
 */
final class Atomik implements ArrayAccess
{
    public static $rootDirectory;

    /**
     * Global store
     * 
     * This property is used to stored all data accessed using get(), set()...
     *
     * @var array
     */
    public static $store = array();
    
    /**
     * Atomik singleton
     *
     * @var Atomik
     */
    private static $instance;
    
    /**
     * Global store to reset to
     * 
     * @var array
     */
    private static $reset = array();
    
    /**
     * Loaded plugins
     * 
     * When a plugin is loaded, its name is saved in this array to 
     * avoid loading it twice.
     *
     * @var array
     */
    private static $plugins = array();
    
    /**
     * Registered events
     * 
     * The array keys are event names and their value is an array with 
     * the event callbacks
     *
     * @var array
     */
    private static $events = array();
    
    /**
     * Execution contexts
     * 
     * Each call to Atomik::execute() creates a context.
     * 
     * @var array
     */
    private static $execContexts = array();
    
    /**
     * Pluggable applications
     * 
     * @var array
     */
    private static $pluggableApplications = array();
    
    /**
     * Already loaded helpers
     * 
     * @var array
     */
    private static $loadedHelpers = array();
    
    /**
     * Returns a singleton instance
     *
     * @return Atomik
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new Atomik();
        }
        return self::$instance;
    }
    
    /**
     * Starts Atomik
     * 
     * If dispatch is false, you will have to manually dispatch the request and exit.
     * 
     * @param string $env A configuration key which will be merged at the root of the store
     * @param string $uri
     * @param bool $dispatch Whether to dispatch
     */
    public static function run($rootDirectory = '.', $env = null, $uri = null, $dispatch = true)
    {
        self::$rootDirectory = $rootDirectory;

        // wrap the whole app inside a try/catch block to catch all errors
        try {
        
            // config & environment
            try {
                self::set(self::loadConfig(self::path(self::get('atomik.files.config'), null, false), false));
            } catch (AtomikException $e) {}
            
            $env = $env ?: (defined('ATOMIK_ENV') ? ATOMIK_ENV : null);
            if ($env !== null && self::has($env)) {
                self::set(self::get($env));
            }
            
            self::fireEvent('Atomik::Config');
            
            // sets the error reporting to all errors if debug mode is on
            if (self::get('atomik.debug', false) == true) {
                error_reporting(E_ALL | E_STRICT);
            }

            // makes relative include dirs relative to app root
            self::set('atomik.dirs.includes', self::path(self::get('atomik.dirs.includes', array())));
            
            // registers the class autoload handler
            if (self::get('atomik.class_autoload', true) == true) {
                if (!function_exists('spl_autoload_register')) {
                    throw new AtomikException("Missing 'spl_autoload_register()' function");
                }
                spl_autoload_register('Atomik::needed');
            }
        
            // cleans the plugins array
            $plugins = array();
            foreach (self::get('plugins', array()) as $key => $value) {
                if (!is_string($key)) {
                    $key = $value;
                    $value = array();
                }
                $plugins[ucfirst($key)] = (array) $value;
            }
            self::set('plugins', $plugins, false);
            
            // loads plugins
            // this method allows plugins that are being loaded to modify the plugins array
            $loadedPlugins = array();
            while (count($pluginsToLoad = array_diff(array_keys(self::get('plugins')), 
                self::getLoadedPlugins(), $loadedPlugins)) > 0) {
                    foreach ($pluginsToLoad as $plugin) {
                        if (self::loadPlugin($plugin) === false) {
                            $loadedPlugins[] = $plugin;
                        }
                    }
            }
            
            self::fireEvent('Atomik::Bootstrap');
            if ($filename = self::path(self::get('atomik.files.bootstrap'))) {
                include $filename;
            }
        
            $cancel = false;
            self::fireEvent('Atomik::Start', array(&$cancel));
            if ($cancel) {
                self::end(true);
            }
        
            if (!self::has('atomik.url_rewriting')) {
                self::set('atomik.url_rewriting', 
                    isset($_SERVER['REDIRECT_URL']) || isset($_SERVER['REDIRECT_URI']));
            }
            
            if ($dispatch) {
                self::dispatch($uri);
                self::end(true);
            }
            
        } catch (Exception $e) {
            $cancel = false;
            self::fireEvent('Atomik::Error', array($e, &$cancel));
            if (!$cancel) {
                throw $e;
            }
        }
    }
    
    /**
     * Loads a configuration file
     *
     * Supported format are php, ini and json
     * If the file's extension is not specified, the method will
     * search for a file with one of the supported extensions.
     *
     * @param string $filename
     * @return array
     */
    public static function loadConfig($filename)
    {
        self::fireEvent('Atomik::Loadconfig::Before', array(&$filename));
        
        // config file format
        if (!preg_match('/.+\.(php|ini|json)$/', $filename)) {
            $found = false;
            foreach (array('php', 'ini', 'json') as $format) {
                if (file_exists("$filename.$format")) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw new AtomikException("Configuration file '$filename' not found");
            }
            $filename .= ".$format";
        } else {
            $format = substr($filename, strrpos($filename, '.') + 1);
        }
        
        $config = array();
        if ($format === 'ini') {
            if (($data = parse_ini_file($filename, true)) === false) {
                throw new AtomikException('INI configuration malformed');
            }
            $config = self::dimensionizeArray($data, '.');
        } else if ($format === 'json') {
            if (($config = json_decode(file_get_contents($filename), true)) === null) {
                throw new AtomikException('JSON configuration malformed');
            }
        } else {
            if (is_array($return = include($filename))) {
                $config = $return;
            }
        }
        
        self::fireEvent('Atomik::Loadconfig::After', array($filename, &$config));
        return $config;
    }
    
    /**
     * Dispatches the request
     * 
     * It takes an URI, applies routes, executes the action and renders the view.
     * If $uri is null, the value of the GET parameter specified as the trigger 
     * will be used.
     * 
     * @param string $uri
     * @param bool $allowPluggableApplication Whether to allow plugin application to be loaded
     */
    public static function dispatch($uri = null, $allowPluggableApplication = true)
    {
        try {
            self::fireEvent('Atomik::Dispatch::Start', array(&$uri, &$allowPluggableApplication, &$cancel));
            if ($cancel) {
                return;
            }
            
            if ($uri === null) {
                $trigger = self::get('atomik.trigger', 'action');
                if (isset($_GET[$trigger]) && !empty($_GET[$trigger])) {
                    $uri = trim($_GET[$trigger], '/');
                }
            }
        
            // retreives the base url
            if (self::get('atomik.base_url') === null) {
                if (self::get('atomik.url_rewriting') && (isset($_SERVER['REDIRECT_URL']) || isset($_SERVER['REDIRECT_URI']))) {
                    // finds the base url from the redirected url
                    $redirectUrl = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : $_SERVER['REDIRECT_URI'];
                    if (isset($_GET[$trigger])) {
                        self::set('atomik.base_url', substr($redirectUrl, 0, -strlen($_GET[$trigger])));
                    } else {
                        self::set('atomik.base_url', $redirectUrl);
                    }
                } else {
                    // finds the base url from the script name
                    self::set('atomik.base_url', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/');
                }
            }
            
            // default uri
            if (empty($uri)) {
                $uri = self::get('app.default_action', 'index');
            }

            $request = $_GET;
            if ($router = self::get('app.router')) {
                $request = call_user_func($router, $uri, $_GET);
            }
                
            // checking if no dot are in the action name to avoid any hack attempt and if no 
            // underscore is use as first character in a segment
            if (strpos($request['action'], '..') !== false || substr($request['action'], 0, 1) === '_' 
                || strpos($request['action'], '/_') !== false) {
                    throw new AtomikException('Action outside of bound');
            }
            
            self::set('request_uri', $uri);
            self::set('request', $request);
            if (!self::has('full_request_uri')) {
                self::set('full_request_uri', $uri);
            }
            
            self::fireEvent('Atomik::Dispatch::Uri', array(&$uri, &$request, &$cancel));
            if ($cancel) {
                return;
            }
            
            // checks if the uri triggers a pluggable application
            if ($allowPluggableApplication) {
                foreach (self::$pluggableApplications as $plugin => $pluggAppConfig) {
                    if (!self::uriMatch($pluggAppConfig['route'], $request['action'])) {
                        continue;
                    }
                    
                    // rewrite uri
                    $baseAction = trim($pluggAppConfig['route'], '/*');
                    $uri = substr(trim($request['action'], '/'), strlen($baseAction));
                    if ($baseAction === '' && $uri === self::get('app.default_action')) {
                        $uri = '';
                    }
                    self::set('atomik.base_action', $baseAction);
                    
                    // dispatches the pluggable application
                    return self::dispatchPluggableApplication($plugin, $uri, $pluggAppConfig);
                }
            }
            
            // fetches the http method
            $httpMethod = $_SERVER['REQUEST_METHOD'];
            if (($param = self::get('app.http_method_param', false)) !== false) {
                // checks if the route parameter to override the method is defined
                $httpMethod = strtoupper(self::get($param, $httpMethod, $request));
            }
            if (!in_array($httpMethod, self::get('app.allowed_http_methods'))) {
                throw new AtomikException('HTTP method not allowed');
            }
            self::set('app.http_method', strtoupper($httpMethod));
            
            // sets the view context
            self::setViewContext();
        
            // configuration is ok, ready to dispatch
            self::fireEvent('Atomik::Dispatch::Before', array(&$cancel));
            if ($cancel) {
                return;
            }
            
            $vars = array();
            if ($filename = self::path(self::get('atomik.files.pre_dispatch'))) {
                list($content, $vars) = self::instance()->scoped($filename);
            }
            
            // executes the action
            ob_start();
            list($content, $vars) = self::execute(self::get('request.action'), true, $vars);
            $content = ob_get_clean() . $content;
            
            // whether to propagate vars to the layout
            if (!self::get('app.vars_to_layout', true)) {
                $vars = array();
            }
            
            // renders the layouts if enabled
            if (($layout = self::get('app.layout', false)) !== false && 
                !self::get('app.disable_layout', false)) {
                    $content = self::renderLayout($layout, $content, $vars);
            }
            
            self::fireEvent('Atomik::Output::Before', array(&$content));
            echo $content;
            self::fireEvent('Atomik::Output::After', array($content));
        
            self::fireEvent('Atomik::Dispatch::After');
        
            if ($filename = self::path(self::get('atomik.files.post_dispatch'))) {
                require($filename);
            }
            
        } catch (AtomikHttpException $e) {
            $cancel = false;
            self::fireEvent('Atomik::Httperror', array($e, &$cancel));
            if (!$cancel) {
                header('Location: ', false, $e->getCode());
                self::end(false);
            }
        }
    }

    /**
     * Fires the Atomik::End event and exits the application
     *
     * @param bool $success Whether the application exit on success or because an error occured
     * @param bool $writeSession Whether to call session_write_close() before exiting
     */
    public static function end($success = false, $writeSession = true)
    {
        self::fireEvent('Atomik::End', array($success, &$writeSession));
        if (isset($_SESSION) && $writeSession) {
            session_write_close();
        }
        exit;
    }
    
    /**
     * Checks if an uri matches the pattern. 
     * 
     * The pattern can contain the * wildcard in any segment.
     * For example "users/*" will match all child actions of users.
     * If you want to match users and its children use "users*".
     * 
     * Pattern is considered a regular expression if enclosed
     * between # (example: "#users/(.*)#")
     * 
     * @param string $pattern
     * @param string $uri Default is the current request uri
     * @return bool
     */
    public static function uriMatch($pattern, $uri = null)
    {
        if ($uri === null) {
            $uri = self::get('request_uri');
        }
        $uri = trim($uri, '/');
        $pattern = trim($pattern, '/');
        
        $regexp = $pattern;
        if ($pattern{0} != '#') {
            $regexp = '#^' . str_replace('*', '(.*)', $pattern) . '$#';
        }
        
        return (bool) preg_match($regexp, $uri);
    }
    
    /**
     * Parses an uri to extract parameters
     * 
     * Routes defines how to extract parameters from an uri. They can
     * have additional default parameters.
     * There are two kind of routes:
     *
     *  - segments: 
     *    the uri is divided into path segments. Each segment can be
     *    either static or a parameter (indicated by :).
     *    eg: /archives/:year/:month
     *
     *  - regexp:
     *    uses a regexp against the uri. Must be enclosed using # instead of
     *    slashes parameters must be specified as named subpattern.
     *    eg: #^archives/(?P<year>[0-9]{4})/(?P<month>[0-9]{2})$#
     *
     * If no route matches, the default route (ie :action) will automatically be used.
     * If the route ends with *, any additional segments will be added as parameters
     * eg: /archives/:year/* + /archives/2009/id/1 => year=2009 id=1
     * 
     * You can also name your routes using the @name parameter (which won't be included
     * in the returned params). Named route can then be used with Atomik::url()
     *
     * @param string $uri
     * @param array $params Additional parameters which are not in the uri
     * @param array $routes Uses app/routes if null
     * @return array Route parameters
     */
    public static function route($uri, $params = array(), $routes = null)
    {
        $routes = $routes ?: self::get('app.routes', array());
        
        self::fireEvent('Atomik::Router::Start', array(&$uri, &$routes, &$params));
        
        // extracts uri information
        $components = parse_url($uri);
        $uri = trim($components['path'], '/');
        if (isset($components['query'])) {
            parse_str($components['query'], $query);
            $params = array_merge($query, $params);
        }
        
        $uriExtension = false;
        if (($dot = strrpos($uri, '.')) !== false) {
            $uriExtension = substr($uri, $dot + 1);
        }
        
        // checks if the extension must be present
        if (self::get('app.force_uri_extension', false) && $uriExtension === false) {
            throw new AtomikException('Missing file extension');
        }
        
        // searches for a route matching the uri
        $found = false;
        $request = array();
        foreach (array_reverse($routes) as $pattern => $defaults) {
            if (!is_array($defaults)) {
                $defaults = array('action' => $defaults);
            }
            if (isset($defaults['@name'])) {
            	unset($defaults['@name']);
            }
        
            if ($pattern{0} !== '#') {
                $pattern = trim(str_replace('/*/', '/(.+)/', $pattern), '/');
                if (preg_match_all('#(([/.]):([a-z_0-9]+))#i', $pattern, $matches)) {
                    for ($i = 0, $c = count($matches[0]); $i < $c; $i++) {
                        $param = $matches[3][$i];
                        $paramRegexp = preg_quote($matches[2][$i], '#') . "(?P<$param>[^/]+)";
                        if (array_key_exists($param, $defaults)) {
                            $paramRegexp = "($paramRegexp|)";
                        }
                        $pattern = str_replace($matches[1][$i], $paramRegexp, $pattern);
                    }
                }
                if (substr($pattern, -2) === '/*' || self::get('atomik.auto_uri_wildcard', false)) {
                    if (substr($pattern, -2) === '/*') {
                        $pattern = substr($pattern, 0, -2);
                    }
                    $pattern .= '(/(?P<unmatched_segments>.+)|)';
                }
                $pattern = "#^$pattern\$#";
            }

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }
            unset($matches[0]);
            
            if (isset($matches['unmatched_segments']) && !empty($matches['unmatched_segments'])) {
                $segments = explode('/', $matches['unmatched_segments']);
                for ($i = 0, $c = count($segments); $i < $c; $i += 2) {
                    if (isset($segments[$i + 1])) {
                        $matches[$segments[$i]] = $segments[$i + 1];
                    } else {
                        $matches[] = $segments[$i];
                    }
                }
            }
            
            $request = array_merge($defaults, $matches);
            $found = true;
            break;
        }
        
        if (!$found) {
            $request = array(
                'action' => $uri, 
                self::get('app.views.context_param', 'format') => $uriExtension === false ? 
                    self::get('app.views.default_context', 'html') : $uriExtension
            );
        }
        
        if (!isset($request['action'])) {
            throw new AtomikException("Missing 'action' parameter in matching route");
        }
        
        $request = array_merge($params, $request);
        self::fireEvent('Atomik::Router::End', array($uri, &$request));
        
        return $request;
    }
    
    
    /* -------------------------------------------------------------------------------------------
     *  Actions
     * ------------------------------------------------------------------------------------------ */
    
    /**
     * Sets the view context
     * 
     * View contexts are defined in app/views/contexts. 
     * They can specify:
     * 	- an extension prefix (prefix)
     *  - a layout (layout) (false disables the layout)
     *  - an HTTP Content-Type (content_type)
     * 
     * @param string $context
     */
    public static function setViewContext($context = null)
    {
        if ($context === null) {
            $context = self::get(self::get('app.views.context_param', 'format'), 
                                self::get('app.views.default_context', 'html'), 
                                self::get('request'));
        }
        
        self::set('app.view_context', $context);
        
        if (($viewContextParams = self::get("app.views.contexts.$context", false)) !== false) {
            if ($viewContextParams['layout'] !== true) {
                self::set('app.layout', $viewContextParams['layout']);
            }
            header('Content-type: ' . 
                self::get('content_type', 'text/html', $viewContextParams));
        }
    }
    
    /**
     * Executes an action using the executor specified in app/executor
     *
     * Tries to execute the action. If this fail, it tries to render the view.
     * If neither of them are found, it will throw an exception.
     *
     * @see Atomik::render()
     * @param string $action The action name. The HTTP method can be suffixed after a dot
     * @param bool|string $viewContext The view context. Set to false to not render the view and return the variables or to true for the request's context
     * @param array $vars
     * @return array A tuple (output, vars)
     */
    public static function execute($action, $viewContext = true, $vars = array())
    {
        $view = $action;
        $render = self::get('app.views.auto', true) && $viewContext !== false;
        
        if (is_bool($viewContext)) {
            $viewContext = self::get('app.view_context');
        }
        $prefix = self::get("app.views.contexts.${viewContext}.suffix", $viewContext);
        if (!empty($prefix)) {
            $view .= '.' . $prefix;
        }
        
        // the execution context allows to make nested calls to execute()
        $context = array('action' => &$action, 'view' => &$view, 'vars' => &$vars,
                            'render' => &$render, 'executor' => &$executor);
        self::$execContexts[] =& $context;
    
        self::fireEvent('Atomik::Execute::Start', array(&$action, &$context, &$vars));
        if ($action === false) {
            self::trigger404('No action specified');
        }
        
        if (($dot = strrpos($action, '.')) !== false) {
            $method = strtolower(substr($action, $dot + 1));
            $action = substr($action, 0, $dot);
        } else {
            $method = strtolower(self::get('app.http_method'));
        }
        $context['method'] = &$method;
    
        self::fireEvent('Atomik::Execute::Before', array(&$action, &$context, &$vars));
        
        $executor = self::get('app.executor', 'Atomik::executeFile');
        $actionExists = true;
        if (($vars = call_user_func($executor, $action, $method, $vars, $context)) === false) {
            $vars = array();
            $actionExists = false;
        }
        
        self::fireEvent('Atomik::Execute::After', array($action, &$context, &$vars));
        array_pop(self::$execContexts);
        
        if ($render === false) {
            return array('', $vars);
        }
        if (($content = self::render($view, $vars)) === false) {
            if (!$actionExists) {
                self::trigger404("No files found for action '$action'");
            }
            $content = '';
        }
        return array($content, $vars);
    }
    
    /**
     * Executor which uses files to define actions
     *
     * Searches for a file called after the action (with the php extension) inside
     * directories set under atomik/dirs/actions
     *
     * The content of this file can be anything.
     *
     * You can create an action file per http method by suffixing the action
     * name by the http method in lower case with a dot separating them. 
     * (eg: submit action for POST => submit.post.php)
     * The non-http-method specific file (ie without any suffix) will always
     * be executed before the http-method specific file and variables will
     * be forwarded from one to another.
     * 
     * @internal
     * @param string $action
     * @param string $method
     * @param array  $context
     * @return array
     */
    public static function executeFile($action, $method, $vars, $context)
    {
        $methodAction = $action . '.' . $method;
        $actionFilename = self::actionFilename($action);
        $methodActionFilename = self::actionFilename($methodAction);
        
        self::fireEvent('Atomik::Executefile', array(&$actionFilename, &$methodActionFilename, &$context));
        if ($actionFilename === false && $methodActionFilename === false) {
            return false;
        }
        
        $atomik = self::instance();
        if ($actionFilename !== false) {
            list($content, $vars) = $atomik->scoped($actionFilename, $vars);
            echo $content;
        }
        if ($methodActionFilename !== false) {
            list($content, $vars) = $atomik->scoped($methodActionFilename, $vars);
            echo $content;
        }
        
        return $vars;
    }
    
    /**
     * Returns an action's filename
     * 
     * @see Atomik::path()
     * @param string $action Action name
     * @param array $dirs Directories where actions are stored (default is using configuration)
     * @return string
     */
    public static function actionFilename($action, $dirs = null, $useNamespaces = false)
    {
        $dirs = self::path($dirs ?: self::get('atomik.dirs.actions'));
        if (($filename = self::findFile("$action.php", $dirs, $useNamespaces)) === false) {
            return $useNamespaces ? false : self::findFile("$action/index.php", $dirs);
        }
        return $filename;
    }
    
    /**
     * Prevents the view of the action from which it's called to be rendered
     */
    public static function noRender()
    {
        if (count(self::$execContexts)) {
            self::$execContexts[count(self::$execContexts) - 1]['render'] = false;
        }
    }
    
    /**
     * Modifies the view associted to the action from which it's called
     * 
     * @param string $view View name
     */
    public static function setView($view)
    {
        if (count(self::$execContexts)) {
            self::$execContexts[count(self::$execContexts) - 1]['view'] = $view;
        }
    }
    
    /**
     * Disables the layout
     * 
     * @param bool $disable Whether to disable the layout
     */
    public static function disableLayout($disable = true)
    {
        self::set('app.disable_layout', $disable);
    }
    
    
    /* -------------------------------------------------------------------------------------------
     *  Views
     * ------------------------------------------------------------------------------------------ */
    
    /**
     * Renders a view
     * 
     * Searches for a file called after the view inside
     * directories configured in atomik/dirs/views. If no file is found, an 
     * exception is thrown unless $triggerError is false.
     *
     * @param string $view The view name
     * @param array $vars An array containing key/value pairs that will be transformed to variables accessible inside the view
     * @param array $dirs Directories where view files are stored
     * @return string|bool
     */
    public static function render($view, $vars = array(), $dirs = null)
    {
        $dirs = $dirs ?: self::get('atomik.dirs.views');
        self::fireEvent('Atomik::Render::Start', array(&$view, &$vars, &$dirs, &$triggerError));
        if ($view === false || ($filename = self::viewFilename($view, $dirs)) === false) {
            return false;
        }
        
        self::fireEvent('Atomik::Render::Before', array(&$view, &$vars, &$filename));
        $output = self::renderFile($filename, $vars);
        self::fireEvent('Atomik::Render::After', array($view, &$output, $vars, $filename));
        return $output;
    }
    
    /**
     * Renders a file using a filename which will not be resolved.
     *
     * @param string $filename Filename
     * @param array $vars An array containing key/value pairs that will be transformed to variables accessible inside the file
     * @return string The output of the rendered file
     */
    public static function renderFile($filename, $vars = array())
    {
        self::fireEvent('Atomik::Renderfile::Before', array(&$filename, &$vars));
        
        if (($callback = self::get('app.views.engine', false)) !== false) {
            if (!is_callable($callback)) {
                throw new AtomikException('The specified rendering engine callback cannot be called');
            }
            $output = $callback($filename, $vars);
            
        } else {
            list($output, $vars) = self::instance()->scoped($filename, $vars, self::get('app.views.short_tags', true));
        }
        
        self::fireEvent('Atomik::Renderfile::After', array($filename, &$output, $vars));
        return $output;
    }
    
    /**
     * Renders a layout
     * 
     * @param string $layout Layout name
     * @param string $content The content that will be available in the layout in the $contentForLayout variable
     * @param array $vars An array containing key/value pairs that will be transformed to variables accessible inside the layout
     * @param array $dirs Directories where to search for layouts
     * @return string
     */
    public static function renderLayout($layout, $content, $vars = array(), $dirs = null)
    {
        $dirs = $dirs ?: self::get('atomik.dirs.layouts');
        if (is_array($layout)) {
            foreach (array_reverse($layout) as $l) {
                $content = self::renderLayout($l, $content, $vars, $dirs);
            }
            return $content;
        }
        
        // allows rendered files to dynamically add layouts
        $appLayout = self::delete('app.layout');
        self::set('app.layout', array($layout));
        
        do {
            $layout = array_shift(self::getRef('app.layout'));
            self::fireEvent('Atomik::Renderlayout', array(&$layout, &$content, &$vars, &$dirs));
            $vars['contentForLayout'] = $content;
            $content = self::render($layout, $vars, $dirs);
        } while (count(self::get('app.layout')));
        
        self::set('app.layout', $appLayout);
        return $content;
    }
    
    /**
     * Returns a view's filename
     * 
     * @see Atomik::path()
     * @param string $view View name
     * @param array $dirs Directories where views are stored (default is using configuration)
     * @param string $extension View's file extension
     * @return string
     */
    public static function viewFilename($view, $dirs = null, $extension = null)
    {
        $dirs = self::path($dirs ?: self::get('atomik.dirs.views'));
        $extension = $extension ?: ltrim(self::get('app.views.file_extension'), '.');
        if (($filename = self::findFile("$view.$extension", $dirs)) === false) {
            return self::findFile("$view/index.$extension", $dirs);
        }
        return $filename;
    }
    
    
    /* -------------------------------------------------------------------------------------------
     *  Helpers
     * ------------------------------------------------------------------------------------------ */
    
    /**
     * Loads an helper file
     * 
     * @param string $helperName
     * @param array $dirs Directories where to search for helpers
     */
    public static function loadHelper($helperName, $dirs = null)
    {
        if (isset(self::$loadedHelpers[$helperName])) {
            return;
        }
        
        self::fireEvent('Atomik::Loadhelper::Before', array(&$helperName, &$dirs));
        $functionName = $helperName;
        $camelizedHelperName = str_replace(' ', '', ucwords(str_replace('_', ' ', $helperName)));
        $className = $camelizedHelperName . 'Helper';
        
        if (!function_exists($functionName) && !class_exists($className)) {
            $dirs = self::path($dirs ?: self::get('atomik.dirs.helpers'));
            if (($include = self::findFile("$helperName.php", $dirs, true)) === false) {
                throw new AtomikException("Helper '$helperName' not found");
            }
            list($filename, $ns) = $include;
            include $filename;
            $functionName = ltrim("$ns\\$functionName", '\\');
            $className = ltrim("$ns\\$className", '\\');
        }
        
        if (function_exists($functionName)) {
            self::$loadedHelpers[$helperName] = $functionName;
        } else if (class_exists($className, false)) {
            self::$loadedHelpers[$helperName] = array(new $className(), $camelizedHelperName);
        } else {
            throw new AtomikException("Helper '$helperName' file found but no function or class matching the helper name");
        }
        
        self::fireEvent('Atomik::Loadhelper::After', array($helperName, $dirs));
    }
    
    /**
     * Registers an helper
     *
     * @param string $helperName
     * @param callback $callback
     */
    public static function registerHelper($helperName, $callback)
    {
         self::$loadedHelpers[$helperName] = $callback;
    }
    
    /**
     * Executes an helper
     * 
     * @param string $helperName
     * @param array $args Arguments for the helper
     * @param array $dirs Directories where to search for helpers
     * @return mixed
     */
    public static function helper($helperName, $args = array(), $dirs = null)
    {
        self::loadHelper($helperName, $dirs);
        return call_user_func_array(self::$loadedHelpers[$helperName], $args);
    }
    
    /**
     * PHP magic method to handle calls to helper in views
     * 
     * @param string $helperName
     * @param array $args
     * @return mixed
     */
    public function __call($helperName, $args)
    {
        if (method_exists('Atomik', $helperName)) {
            return call_user_func_array(array('Atomik', $helperName), $args);
        }
        return self::helper($helperName, $args);
    }
    
    /**
     * PHP magic method to handle calls to undefined method
     * Redirect calls to {@see Atomik::helper()}
     * 
     * @param string $helperName
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($helperName, $args)
    {
        return call_user_func(array(self::instance(), 'helper'), $helperName, $args);
    }
    
    
    /* -------------------------------------------------------------------------------------------
     *  Accessors
     * ------------------------------------------------------------------------------------------ */
    
    /**
     * Sets a key/value pair in the store
     * 
     * If the first argument is an array, values are merged recursively.
     * The array is first dimensionized
     * You can set values from sub arrays by using a path-like key.
     * For example, to set the value inside the array $array[key1][key2]
     * use the key 'key1.key2'
     * Can be used on any array by specifying the third argument
     *
     * @see Atomik::dimensionizeArray()
     * @param array|string $key Can be an array to set many key/value
     * @param mixed $value
     * @param bool $dimensionize Whether to use Atomik::dimensionizeArray() on $key
     * @param array $array The array on which the operation is applied
     * @param array $add Whether to add values or replace them
     */
    public static function set($key, $value = null, $dimensionize = true, &$array = null, $add = false)
    {
        if ($array === null) {
            $array = &self::$store;
        }
        
        // setting a key directly
        if (is_string($key)) {
            $segments = self::splitArrayPath($key);
            $key = array_pop($segments);
            $parentArrayKey = count($segments) ? implode('.', $segments) : null;
            
            $parentArray = &self::getRef($parentArrayKey, $array);
            if ($parentArray === null) {
                $dimensionizedParentArray = self::dimensionizeArray(array($parentArrayKey => null));
                $array = self::mergeRecursive($array, $dimensionizedParentArray);
                $parentArray = &self::getRef($parentArrayKey, $array);
            }
            
            if ($add !== false) {
                if (!isset($parentArray[$key]) || $parentArray[$key] === null) {
                    if (!is_array($value)) {
                        $parentArray[$key] = $value;
                        return;
                    }
                    $parentArray[$key] = array();
                } else if (!is_array($parentArray[$key])) {
                    $parentArray[$key] = array($parentArray[$key]);
                }
                
                $value = is_array($value) ? $value : array($value);
                if ($add == 'prepend') {
                    $parentArray[$key] = array_merge_recursive($value, $parentArray[$key]);
                } else {
                    $parentArray[$key] = array_merge_recursive($parentArray[$key], $value);
                }
            } else {
                $parentArray[$key] = $value;
            }
            
            return;
        }
        
        if (!is_array($key)) {
            throw new AtomikException("The first parameter of Atomik::set() must be a string or an array, '" . gettype($key) . "' given");
        }
        
        if ($dimensionize) {
            $key = self::dimensionizeArray($key);
        }
    
        // merges the store and the array
        if ($add) {
            $array = array_merge_recursive($array, $key);
        } else {
            $array = self::mergeRecursive($array, $key);
        }
    }
    
    /**
     * Adds a value to the array pointed by the key
     * 
     * If the first argument is an array, values are merged recursively.
     * The array is first dimensionized
     * You can add values to sub arrays by using a path-like key.
     * For example, to add a value to the array $array[key1][key2]
     * use the key 'key1.key2'
     * If the value pointed by the key is not an array, it will be
     * transformed to one.
     * Can be used on any array by specifying the third argument
     *
     * @see Atomik::dimensionizeArray()
     * @param array|string $key Can be an array to add many key/value
     * @param mixed $value
     * @param bool $dimensionize Whether to use Atomik::dimensionizeArray()
     * @param array $array The array on which the operation is applied
     */
    public static function add($key, $value = null, $dimensionize = true, &$array = null)
    {
        return self::set($key, $value, $dimensionize, $array, 'append');
    }
    
    /**
     * Prependes a value to the array pointed by the key
     * 
     * Works the same as add()
     *
     * @see Atomik::add()
     * @param array|string $key Can be an array to add many key/value
     * @param mixed $value
     * @param bool $dimensionize Whether to use Atomik::dimensionizeArray()
     * @param array $array The array on which the operation is applied
     */
    public static function prepend($key, $value = null, $dimensionize = true, &$array = null)
    {
        return self::set($key, $value, $dimensionize, $array, 'prepend');
    }
    
    /**
     * Like array_merge() but recursively
     *
     * @internal
     * @see array_merge()
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function mergeRecursive($array1, $array2)
    {
        $array = $array1;
        foreach ($array2 as $key => $value) {
            if (is_array($value) && array_key_exists($key, $array1) && is_array($array1[$key])) {
                $array[$key] = self::mergeRecursive($array1[$key], $value);
                continue;
            }
            $array[$key] = $value;
        }
        return $array;
    }

    /**
     * Splits array path into segments
     * 
     * @param string $path
     * @param string $separator
     * @return string
     */
    public static function splitArrayPath($path, $separator = '.')
    {
        return array_filter(explode($separator, $path));
    }

    /**
     * Checks if an array or an ArrayAccess object has the specitied key
     *
     * Same as array_key_exists() but supports ArrayAccess
     * 
     * @param string $key
     * @param array $array
     * @return bool
     */
    public static function arrayHasKey($key, $array)
    {
        return ($array instanceof \ArrayAccess && $array->offsetExists($key)) || array_key_exists($key, $array);
    }
    
    /**
     * Recursively checks array for path-like keys (ie. keys containing dots)
     * and transform them into multi dimensions array
     *
     * @internal
     * @param array $array
     * @param string $separator
     * @return array
     */
    public static function dimensionizeArray($array, $separator = '.')
    {
        $dimArray = array();
        
        foreach ($array as $key => $value) {
            $key = trim($key, $separator);
            // checks if the key is a path
            if (strpos($key, $separator) !== false) {
                $parts = self::splitArrayPath($key, $separator);
                $firstPart = array_shift($parts);
                // recursively dimensionize the key
                $value = self::dimensionizeArray(array(implode($separator, $parts) => $value), $separator);
                
                if (isset($dimArray[$firstPart])) {
                    if (!is_array($dimArray[$firstPart])) {
                        // if $firstPart exists but is not an array, drops the value and use an array
                        $dimArray[$firstPart] = array();
                    }
                    // merge recursively both arrays
                    $dimArray[$firstPart] = self::mergeRecursive($dimArray[$firstPart], $value);
                } else {
                    $dimArray[$firstPart] = $value;
                }
                
            } else if (is_array($value)) {
                // dimensionize sub arrays
                $value = self::dimensionizeArray($value, $separator);
                if (isset($dimArray[$key])) {
                    $dimArray[$key] = self::mergeRecursive($dimArray[$key], $value);
                } else {
                    $dimArray[$key] = $value;
                }
            } else {
                $dimArray[$key] = $value;
            }
        }
        
        return $dimArray;
    }
    
    /**
     * Gets a value using its associatied key from the store
     * 
     * You can fetch value from sub arrays by using a path-like
     * key. Separate each key with a slash. For example if you want 
     * to fetch the value from an $store[key1][key2][key3] you can use
     * key1.key2.key3
     * Can be used on any array by specifying the third argument
     *
     * @param string|array $key The configuration key which value should be returned. If null, fetches all values
     * @param mixed $default Default value if the key is not found
     * @param array $array The array on which the operation is applied
     * @return mixed
     */
    public static function get($key = null, $default = null, $array = null)
    {
        if (($value = self::getRef($key, $array)) !== null) {
            return $value;
        }
        return $default;
    }
    
    /**
     * Checks if a key is defined in the store
     * 
     * Can check through sub array using a path-like key
     * Can be used on any array by specifying the second argument
     *
     * @see Atomik::get()
     * @param string $key The key which should be deleted
     * @param array $array The array on which the operation is applied
     * @return bool
     */
    public static function has($key, $array = null)
    {
        $segments = self::splitArrayPath($key);
        $key = array_pop($segments);
        $parentArrayKey = count($segments) ? implode('.', $segments) : null;
        $parentArray = self::getRef($parentArrayKey, $array);
        return !empty($parentArray) && self::arrayHasKey($key, $parentArray);
    }
    
    /**
     * Deletes a key from the store
     * 
     * Can delete through sub array using a path-like key
     * Can be used on any array by specifying the second argument
     *
     * @see Atomik::get()
     * @param string $key
     * @param array $array The array on which the operation is applied
     * @return mixed The deleted value
     */
    public static function delete($key, &$array = null)
    {
        $segments = self::splitArrayPath($key);
        $key = array_pop($segments);
        $parentArrayKey = count($segments) ? implode('.', $segments) : null;
        $parentArray = &self::getRef($parentArrayKey, $array);
        
        if ($parentArray === null || !self::arrayHasKey($key, $parentArray)) {
            throw new AtomikException("Key '$key' does not exists");
        }
        
        $value = $parentArray[$key];
        unset($parentArray[$key]);
        return $value;
    }
    
    /**
     * Gets a reference to a value from the store using its associatied key
     * 
     * You can fetch value from sub arrays by using a path-like
     * key. Separate each key with a slash. For example if you want 
     * to fetch the value from an $store[key1][key2][key3] you can use
     * key1.key2.key3
     * Can be used on any array by specifying the second argument
     *
     * @param string|array $key The configuration key which value should be returned. If null, fetches all values
     * @param array $array The array on which the operation is applied
     * @return mixed Null if the key does not match
     */
    public static function &getRef($key = null, &$array = null)
    {
        $null = null;
        if ($array === null) {
            $array = &self::$store;
        }
        if ($key === null) {
            return $array;
        }
        
        if (!is_array($key)) {
            if (!strpos($key, '.')) {
                if (self::arrayHasKey($key, $array)) {
                    $value =& $array[$key];
                    return $value;
                }
                return $null;
            }
            $key = self::splitArrayPath($key);
        }
        
        $firstKey = array_shift($key);
        if (self::arrayHasKey($firstKey, $array)) {
            if (count($key) > 0) {
                return self::getRef($key, $array[$firstKey]);
            } else if (!is_array($array)) {
                $value = $array[$firstKey];
                return $value;
            } else {
                $value =& $array[$firstKey];
                return $value;
            }
        }
        
        return $null;
    }
    
    /**
     * Resets the global store
     * 
     * If no argument are specified the store is resetted, otherwise value are set normally and the
     * state is saved.
     * 
     * @internal 
     * @see Atomik::set()
     * @param array|string $key Can be an array to set many key/value
     * @param mixed $value
     * @param bool $dimensionize Whether to use Atomik::dimensionizeArray() on $key
     */
    public static function reset($key = null, $value = null, $dimensionize = true)
    {
        if ($key !== null) {
            self::set($key, $value, $dimensionize, self::$reset);
            self::set($key, $value, $dimensionize);
            return;
        }
        
        self::$store = self::mergeRecursive(self::$store, self::$reset);
    }

    public function offsetGet($key)
    {
        return Atomik::get($key);
    }

    public function offsetSet($key, $value)
    {
        return Atomik::set($key, $value);
    }

    public function offsetExists($key)
    {
        return Atomik::has($key);
    }

    public function offsetUnset($key)
    {
        return Atomik::delete($key);
    }
    
    
    /* -------------------------------------------------------------------------------------------
     *  Events
     * ------------------------------------------------------------------------------------------ */
    
    /**
     * Registers a callback to an event
     *
     * @param string $event Event name
     * @param callback $callback The callback to call when the event is fired
     * @param int $priority Listener priority
     * @param bool $important If true and a listener of the same priority already exists, registers the new listener before the existing one. 
     */
    public static function listenEvent($event, $callback, $priority = 50, $important = false)
    {
        if (!isset(self::$events[$event])) {
            self::$events[$event] = array();
        }
        
        // while there is an event with the same priority, checks
        // with an higher or lower priority
        while (isset(self::$events[$event][$priority])) {
            $priority += $important ? -1 : 1;
        }
        
        self::$events[$event][$priority] = $callback;
    }
    
    /**
     * Fires an event
     * 
     * @param string $event The event name
     * @param array $args Arguments for listeners
     * @param bool $resultAsString Whether to return all listener results as a string
     * @return array An array containing results of each executed listeners
     */
    public static function fireEvent($event, $args = array(), $resultAsString = false)
    {
        $results = array();
        
        if (isset(self::$events[$event])) {
            $keys = array_keys(self::$events[$event]); 
            sort($keys);
            foreach ($keys as $key) {
                $callback = self::$events[$event][$key];
                $results[$key] = call_user_func_array($callback, $args);
            }
        }
        
        if ($resultAsString) {
            return implode('', $results);
        }
        return $results;
    }
    
    /**
     * Automatically registers event listeners for methods starting with "on"
     *
     * @param string|object $class
     */
    public static function attachClassListeners($class)
    {
        $methods = get_class_methods($class);
        foreach ($methods as $method) {
            if (preg_match('/^on[A-Z].*$/', $method)) {
                $event = preg_replace('/(?<=\\w)([A-Z])/', '::\1', substr($method, 2));
                self::listenEvent($event, array($class, $method));
            }
        }
    }
    
    
    /* -------------------------------------------------------------------------------------------
     *  Plugins
     * ------------------------------------------------------------------------------------------ */
    
    /**
     * Loads a plugin using the configuration specified under plugins
     * 
     * @param string $name
     * @return bool
     */
    public static function loadPlugin($name)
    {
        $name = ucfirst($name);
        if (($config = &self::getRef("plugins.$name")) === null) {
            $config = array();
        }
        return self::loadCustomPlugin($name, $config);
    }
    
    /**
     * Loads a custom plugin
     *
     * Options:
     *   - dirs:              Directories from where to load the plugin
     *   - classNameTemplate: % will be replaced with the plugin name
     *   - callStart:         Whether to call the start() method on the plugin class
     *
     * @param string $plugin The plugin name
     * @param array $config Configuration for this plugin
     * @param array $options Options for loading this plugin
     * @return bool Success
     */
    public static function loadCustomPlugin($plugin, &$config = array(), $options = array())
    {
        $plugin = ucfirst($plugin);
        if (self::isPluginLoaded($plugin)) {
            return true;
        }
        
        $options = array_merge(array(
            'dirs'                  => self::get('atomik.dirs.plugins'),
            'classNameTemplate'     => '%',
            'dirClassNameTemplate'  => 'Plugin',
            'callStart'             => true
        ), $options);
        $options['dirs'] = self::path($options['dirs']);
        
        self::fireEvent('Atomik::Plugin::Before', array(&$plugin, &$config, &$options));
        if ($plugin === false) {
            return false;
        }
        
        $pluginClass = str_replace('%', $plugin, $options['classNameTemplate']);
        if (!class_exists($pluginClass)) {
            // tries to load the plugin from a file
            $ns = '';
            $filename = str_replace('\\', DIRECTORY_SEPARATOR, $plugin);
            if (($include = self::findFile("$filename.php", $options['dirs'], true)) === false) {
                // no file, checks for a directory
                if (($include = self::findFile($filename, $options['dirs'], true)) === false) {
                    throw new AtomikException("Missing plugin '$plugin' (no file or directory matching plugin name)");
                }
                
                list($pluginDir, $ns) = $include;
                $pluginClass = str_replace('%', $plugin, $options['dirClassNameTemplate']);
                $filename = "$pluginDir/Plugin.php";
                $appFilename = "$pluginDir/Application.php";
                if (!empty($ns)) {
                    $ns = ltrim("$ns\\$plugin", '\\');
                }
                
                if (!($isPluggApp = file_exists($appFilename)) && !file_exists($filename)) {
                    throw new AtomikException("Missing plugin '$plugin' (no file inside the plugin's directory)");
                }
                
                if ($isPluggApp && !isset(self::$pluggableApplications[$plugin])) {
                    self::registerPluggableApplication($plugin);
                }

                if (!empty($ns)) {
                    self::add('atomik.dirs.includes', array($ns => $pluginDir));
                } else {
                    set_include_path($pluginDir . PATH_SEPARATOR . get_include_path());
                }
                
            } else {
                list($filename, $ns) = $include;
                $pluginDir = dirname($filename);
            }
            self::instance()->scoped($filename, array('config' => $config));
            $pluginClass = ltrim("$ns\\$pluginClass", '\\');
        }

        if (class_exists($pluginClass, false)) {
            $registerEventsCallback = true;
            // call the start method on the plugin class if it's defined
            if ($options['callStart'] && method_exists($pluginClass, 'start')) {
                if (call_user_func_array(array($pluginClass, 'start'), array(&$config)) === false) {
                    $registerEventsCallback = false;
                }
            }
            // automatically registers events callback for methods starting with "on"
            if ($registerEventsCallback) {
                self::attachClassListeners($pluginClass);
            }
        }
        
        self::fireEvent('Atomik::Plugin::After', array($plugin));
        
        // stores the plugin name so we won't load it twice 
        // also stores the directory from where it was loaded
        self::$plugins[$plugin] = isset($pluginDir) ? rtrim($pluginDir, DIRECTORY_SEPARATOR) : true;
        return true;
    }
    
    /**
     * Loads a plugin only if it's available
     * 
     * @see Atomik::loadPlugin()
     */
    public static function loadPluginIfAvailable($plugin)
    {
        if (!self::isPluginLoaded($plugin) && self::isPluginAvailable($plugin)) {
            self::loadPlugin($plugin);
        }
    }
    
    /**
     * Loads a custom plugin only if it's available
     * 
     * @see Atomik::loadPlugin()
     */
    public static function loadCustomPluginIfAvailable($plugin, $config = array(), $options = array())
    {
        if (!self::isPluginLoaded($plugin) && self::isPluginAvailable($plugin)) {
            self::loadCustomPlugin($plugin, $config, $options);
        }
    }
    
    /**
     * Checks if a plugin is already loaded
     *
     * @param string $plugin
     * @return bool
     */
    public static function isPluginLoaded($plugin)
    {
        return isset(self::$plugins[ucfirst($plugin)]);
    }
    
    /**
     * Checks if a plugin is available
     *
     * @param string $plugin
     * @return bool
     */
    public static function isPluginAvailable($plugin)
    {
        $plugin = ucfirst($plugin);
        $dirs = self::path(self::get('atomik.dirs.plugins'));
        if (self::findFile("$plugin.php", $dirs) === false) {
            return self::findFile($plugin, $dirs) !== false;
        }
        return true;
    }
    
    /**
     * Returns all loaded plugins
     * 
     * @param bool $withDir Whether to only returns plugin names or the name (as array key) and the directory
     * @return array
     */
    public static function getLoadedPlugins($withDir = false)
    {
        return $withDir ? self::$plugins : array_keys(self::$plugins);
    }
    
    /**
     * Registers a pluggable application
     * 
     * Possible configuration keys are:
     *   - rootDir:             directory inside the plugin directory where the application is stored (default empty string)
     *   - pluginDir:           the plugin's directory (default to null, will find the directory automatically)
     *   - resetConfig:			whether to reset the config before dispatching
     *   - overwriteDirs:       whether to keep access to the user actions, views, layouts and helpers folders
     *   - overwriteFiles:		whether to keep access to the pre_dispatch and post_dispatch files
     *   - checkPluginIsLoaded: whether to check if the plugin is loaded
     *   - bootstrapFile:		the name of the bootstrap file (eg: Application.php)
     * 
     * @param string $plugin Plugin's name
     * @param string $route The route that will trigger the application (default is the plugin name)
     * @param array $config Configuration
     */
    public static function registerPluggableApplication($plugin, $route = null, $config = array())
    {
        $plugin = ucfirst($plugin);
        self::fireEvent('Atomik::Registerpluggableapplication', array(&$plugin, &$route, &$config));
        if (empty($plugin)) {
            return;
        }
        $config['route'] = $route ?: (strtolower($plugin) . '*');
        self::$pluggableApplications[$plugin] = $config;
    }
    
    /**
     * Dispatches a pluggable application
     * 
     * @see Atomik::registerPluggableApplication()
     * @param string $plugin Plugin's name
     * @param string $uri Uri
     * @param array $config Configuration
     * @return bool Dispatch success
     */
    public static function dispatchPluggableApplication($plugin, $uri = null, $config = array())
    {
        $plugin = ucfirst($plugin);
        $config = array_merge(array(
            'rootDir'             => '', 
            'pluginDir'           => null,
            'resetConfig'         => true, 
            'overwriteDirs'       => true, 
            'overwriteFiles'      => true, 
            'checkPluginIsLoaded' => true,
            'bootstrapFile'       => 'Application.php'
        ), $config);
        
        if ($config['checkPluginIsLoaded'] && !self::isPluginLoaded($plugin)) {
            return false;
        }
        
        $uri = $uri ?: '';
        $rootDir = rtrim('/' . trim($config['rootDir'], '/'), '/');
        if ($config['pluginDir'] === null) {
            $pluginDir = self::$plugins[$plugin];
        } else {
            $pluginDir = rtrim($config['pluginDir'], '/');
        }
        
        $appDir = $pluginDir . $rootDir;
        if (!is_dir($appDir)) {
            throw new AtomikException("To be used as an application, the plugin '$plugin' must use a directory");
        }
        
        $overrideDir = self::findFile($plugin . $rootDir, self::path(self::get('atomik.dirs.overrides')));
        if ($overrideDir === false) {
            $overrideDir = 'overrides/' . $plugin . $rootDir;
        }
        
        if (!self::has('app.running_plugin')) {
            // saves user configuration
            self::set('userapp', self::get('app'));
        }
        self::set('app.running_plugin', $plugin); 
        self::set('request_uri', $uri);
        
        // resets the configuration but keep the layout
        if ($config['resetConfig']) {
            $layout = self::get('app.layout');
            self::reset();
            self::set('app.layout', $layout);
            self::set('app.routes', array());
        }
        
        // rewrite dirs
        $dirs = self::get('atomik.dirs');
        $dirs['actions'] = array($overrideDir . '/actions', $appDir . '/actions');
        $dirs['views'] = array($overrideDir . '/views', $appDir . '/views');
        
        $overwritableDirs = array(
            'layouts'   => array($overrideDir . '/layouts', $overrideDir . '/views', 
                                    $appDir . '/layouts', $appDir . '/views')
        );
        
        if ($config['overwriteDirs']) {
            $dirs = array_merge($dirs, $overwritableDirs);
        } else {
            $dirs = array_merge_recursive($overwritableDirs, $dirs);
        }
        
        // do not overwrite helpers
        $dirs['helpers'] = array_merge(
            array($overrideDir . '/helpers', $appDir . '/helpers'),
            (array) $dirs['helpers']
        );
        
        self::set('atomik.dirs', $dirs);
        
        // rewrite files
        if ($config['overwriteFiles']) {
            $files = self::get('atomik.files');
            $files['pre_dispatch'] = $appDir . '/pre_dispatch.php';
            $files['post_dispatch'] = $appDir . '/post_dispatch.php';
            self::set('atomik.files', $files);
        }
        
        $cancel = false;
        self::fireEvent('Atomik::Dispatchpluginapplication::Ready', array($plugin, &$uri, $config, &$cancel));
        if ($cancel) {
            return true;
        }
        
        $bootstrapFile = $appDir . '/' . $config['bootstrapFile'];
        if (file_exists($bootstrapFile)) {
            $continue = include $bootstrapFile;
            if ($continue === false) {
                return true;
            }
        }
        
        $cancel = false;
        self::fireEvent('Atomik::Dispatchpluginapplication::Start', array($plugin, &$uri, $config, &$cancel));
        if ($cancel) {
            return true;
        }
        
        return self::dispatch($uri, false);
    }
    
    
    /* -------------------------------------------------------------------------------------------
     *  Utilities
     * ------------------------------------------------------------------------------------------ */
    
    /**
     * Includes a file in the method scope and returns
     * public variables and the output buffer
     * 
     * @internal
     * @param string $__filename Filename
     * @param array $__vars An array containing key/value pairs that will be transformed to variables accessible inside the file
     * @param bool $__allowShortTags Whether to convert PHP's short tags to standard tags
     * @return array A tuple with the output buffer and the public variables
     */
    private function scoped($__filename, $__vars = array(), $__allowShortTags = false)
    {
        extract((array)$__vars);
        ob_start();
        if ($__allowShortTags && version_compare(PHP_VERSION, '5.4.0', '<') && 
            (bool) @ini_get('short_open_tag') === false) {
            // from CodeIgniter (https://github.com/EllisLab/CodeIgniter)
            eval('?>' . preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($__filename))));
        } else {
            include($__filename);
        }
        $content = ob_get_clean();
        
        // retreives "public" variables (not prefixed with an underscore)
        $vars = array();
        foreach (get_defined_vars() as $name => $value) {
            if (substr($name, 0, 1) != '_') {
                $vars[$name] = $value;
            }
        }
        
        return array($content, $vars);
    }

    /*
     * Includes a file
     *
     * @param string $include Filename or class name following the PEAR convention
     * @param bool $className If true, $include will be transformed from a PEAR-formed class name to a filename
     * @return bool
     */
    public static function needed($include, $className = true)
    {
        if ($className && (class_exists($include, false) || interface_exists($include, false))) {
            return true;
        }

        self::fireEvent('Atomik::Needed', array(&$include, &$className, &$dirs));
        if ($include === null) {
            return false;
        }

        $includeDirs = (array) self::get('atomik.dirs.includes', array());
        if ($filename = self::resolveIncludePath($include, $includeDirs, $className)) {
            return include($filename);
        }
        return false;
    }

    /**
     * Resolves an include name (either a filename or a classname) to a full pathname
     * 
     * @param string $include
     * @param string|array $includeDirs
     * @param boolean $className Whether $include can be a classname
     * @return string
     */
    public static function resolveIncludePath($include, $includeDirs, $className = true)
    {
        $ns = null;
        $dirs = array();
        $include = trim($include, '\\');

        foreach ((array) $includeDirs as $includeNs => $includeDir) {
            if ($ns === null && is_numeric($includeNs)) {
                $dirs[] = $includeDir;
                continue;
            } else if (!$className) {
                continue;
            }
            $includeNs = trim($includeNs, '\\');
            if (substr($include, 0, strlen($includeNs)) === $includeNs) {
                if ($ns !== null && strlen($ns) > strlen($includeNs)) {
                    continue;
                }
                $ns = $includeNs;
                $dirs = $includeDir;
            }
        }

        if ($ns !== null) {
            $include = ltrim(substr($include, strlen($ns)), '\\');
        }
        $include = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $include);
        return self::findFile("$include.php", $dirs);
    }
    
    /**
     * Finds a file in an array of directories
     *
     * @param string $filename
     * @param array $dirs
     * @param bool $isInclude
     * @return string
     */
    public static function findFile($filename, $dirs, $isInclude = false)
    {
        if (empty($filename)) {
            return false;
        }
        foreach (array_reverse((array) $dirs) as $ns => $dir) {
            if ($dir === false) {
                continue;
            }
            if($isInclude && is_numeric($ns)) {
                $ns = '';
            }
            if ($pathname = self::path($filename, $dir)) {
                return $isInclude ? array($pathname, $ns) : $pathname;
            }
        }
        return false;
    }

    /**
     * Makes a filename relative to another one
     *
     * @param string $filename
     * @param string $relativeTo
     * @param bool $checkExists
     * @return string
     */
    public static function path($filename, $relativeTo = null, $checkExists = true, $ds = null)
    {
        $ds = $ds ?: DIRECTORY_SEPARATOR;

        if (is_array($filename)) {
            $pathnames = array();
            foreach ($filename as $k => $f) {
                $pathnames[$k] = self::path($f, $relativeTo, $checkExists);
            }
            return $pathnames;
        }

        $relativeTo = $relativeTo ?: self::$rootDirectory;
        $pathname = $filename;
        if ($filename{0} != '/' && !preg_match('#^[A-Z]:(\\\\|/)#', $filename)) {
            if (strlen($filename) >= 2 && substr($filename, 0, 2) == './') {
                $filename = substr($filename, 2);
            }
            $pathname = rtrim($relativeTo, $ds) . $ds . $filename;
        }
        if ($checkExists) {
            return realpath($pathname);
        }
        return $pathname;
    }
    
    /**
     * Returns an url for the action depending on whether url rewriting
     * is used or not. Will return an url relative to the current application scope.
     * Ie. if a plugin uses this method, it will return an url for an action of itself
     * 
     * Can be used on links starting with a protocol but they will of course
     * not be resolved like action names.
     * Named routes can also be used (only specify the route name - with the @ - as the action)
     *
     * The item "_merge_GET" can be use in $params to merge GET parameters with specified params.
     *
     * @param string $action The action name or an url. Can contain GET parameters (after ?)
     * @param array $params GET parameters to be added to the query string, if true will reuse current GET params
     * @param bool $useIndex Whether to use index.php in the url
     * @param bool $useBaseAction Whether to prepend the action with atomik/base_action
     * @return string
     */
    public static function url($action = null, $params = array(), $useIndex = true, $useBaseAction = true)
    {
        $action = $action ?: self::get('request_uri');
        $trigger = self::get('atomik.trigger', 'action');
        if ($params === false) {
            $params = array();
        }
        
        if ($params === true || in_array('__merge_GET', $params)) {
            if (!is_array($params)) {
                $params = array();
            }
            $GET = $_GET;
            if (isset($GET[$trigger])) {
                unset($GET[$trigger]);
            }
            if (($i = array_search('__merge_GET', $params)) !== false) {
                unset($params[$i]);
            }
            $params = self::mergeRecursive($GET, $params);
        }
        
        // removes the query string from the action
        if (($separator = strpos($action, '?')) !== false) {
            $queryString = parse_url($action, PHP_URL_QUERY);
            $action = substr($action, 0, $separator);
            parse_str($queryString, $actionParams);
            $params = self::mergeRecursive($actionParams, $params);
        }
        
        // checks if it's a named route
        if (strlen($action) > 0 && $action{0} == '@') {
        	$routeName = substr($action, 1);
        	$action = null;
        	foreach (self::get('app.routes') as $route => $default) {
        		if (!is_array($default) || !isset($default['@name']) || 
        			$default['@name'] !== $routeName) {
        			    continue;
        		}
			    if ($route{0} == '#') {
			        throw new AtomikException("Named route ('$routeName') cannot use regular expressions");
			    }
    			$action = $route;
    			break;
        	}
            if ($action === null) {
                throw new AtomikException("Missing route named '$routeName'");
            }
        }
        
        // injects parameters into the url
        if (preg_match_all('/(:([a-z0-9_]+))/i', $action, $matches)) {
            for ($i = 0, $c = count($matches[0]); $i < $c; $i++) {
                if (array_key_exists($matches[2][$i], $params)) {
                    $action = str_replace($matches[1][$i], $params[$matches[2][$i]], $action);
                    unset($params[$matches[2][$i]]);
                }
            }
        }
        
        // checks if $action is not an url (checking if there is a protocol)
        $url = $action;
        if (!preg_match('/^([a-z]+):\/\/.*/', $action)) {
            $action = ltrim($action, '/');
            $url = rtrim(self::get('atomik.base_url', '.'), '/') . '/';
            if ($useBaseAction) {
                $action = ltrim(trim(self::get('atomik.base_action', ''), '/') . '/' . $action, '/');
            }
            if (!$useIndex || self::get('atomik.url_rewriting', false) == true) {
                $url .= $action;
            } else {
                $url .= self::get('atomik.files.index', 'index.php');
                $params[$trigger] = $action;
            }
        }
        
        if (count($params)) {
            $url .= '?' . http_build_query($params, '', '&amp;');
        }
        
        $args = func_get_args();
        unset($args[0]);    
        self::fireEvent('Atomik::Url', array($action, &$url, $args));
        
        return $url;
    }
    
    /**
     * Returns an url exactly like Atomik::url() but relative to the root application.
     *
     * @see Atomik::url()
     * @param string $action
     * @param array $params
     * @param bool $useIndex
     * @return string
     */
    public static function appUrl($action = null, $params = array(), $useIndex = true)
    {
        return self::url($action, $params, $useIndex, false);
    }
    
    /**
     * Returns an url exactly like Atomik::url() but relative to a plugin
     *
     * @see Atomik::url()
     * @param string $plugin The name of a plugin which is used as a pluggable application
     * @param string $action
     * @param array $params
     * @param bool $useIndex
     * @return string
     */
    public static function pluginUrl($plugin, $action, $params = array(), $useIndex = true)
    {
    	$plugin = ucfirst($plugin);
        if (!isset(self::$pluggableApplications[$plugin])) {
            throw new AtomikException("Plugin '$plugin' is not registered as a pluggable application");
        }
        
        $route = rtrim(self::$pluggableApplications[$plugin]['route'], '/*');
        return self::url($route . '/' . ltrim($action, '/'), $params, $useIndex, false);
    }
    
    /**
     * Returns the url of an asset file (ie. an url without index.php) relative
     * to the current application scope
     * 
     * @see Atomik::url()
     * @param string $filename
     * @param array $params
     * @return string
     */
    public static function asset($filename, $params = array())
    {
        if ($plugin = self::get('app.running_plugin')) {
            return self::pluginAsset($plugin, $filename, $params);
        }
        return self::appAsset($filename, $params);
    }
    
    /**
     * Returns the url of an asset file relative to the root application
     * 
     * @see Atomik::asset()
     * @param string $filename
     * @param array $params
     * @return string
     */
    public static function appAsset($filename, $params = array())
    {
        return self::url($filename, $params, false, false);
    }
    
    /**
     * Returns the url of a plugin's asset file following the path template
     * defined in the configuration.
     * 
     * @see Atomik::url()
     * @param string $plugin Plugin's name (default is the currently running pluggable app)
     * @param string $filename
     * @param array $params
     * @return string
     */
    public static function pluginAsset($plugin, $filename, $params = array())
    {
        $template = self::get('atomik.plugin_assets_tpl', 'app/plugins/%s/assets');
        $dirname = rtrim(sprintf($template, ucfirst($plugin)), '/');
        $filename = '/' . ltrim($filename, '/');
        return self::url($dirname . $filename, $params, false, false);
    }
    
    /**
     * Triggers a 404 error
     *
     * @param string $message
     */
    public static function trigger404($message = 'Not found')
    {
        throw new AtomikHttpException($message, 404);
    }
}

/* -------------------------------------------------------------------------------------------
 *  APPLICATION CONFIGURATION
 * ------------------------------------------------------------------------------------------ */

Atomik::reset(array(

    'app' => array(
    
        /* @var string */
        'default_action'        => 'index',

        /* The name of the layout
         * Add multiple layouts using an array (will be rendered in reverse order)
         * @var array|bool|string */
        'layout'                => false,
    
        /* @var bool */
        'disable_layout'        => false,
    
        /* Whether to propagate view vars to the layout
         * @var bool */
        'vars_to_layout'        => true,
        
        /* An array where keys are route names and their value is an associative
         * array of default values
         * @see Atomik::route()
         * @var array */
        'routes'                => array(),
    
        /* @var bool */
        'force_uri_extension'   => false,
        
        /**
         * The callback used to route urls, false to disable
         * @var callback */
        'router'                => 'Atomik::route',
        
        /**
         * The callback used to execute actions
         * @var callback */
        'executor'              => 'Atomik::executeFile',
    
        /* @see Atomik::render()
         * @var array */
        'views' => array(
        
            /* Whether to automatically render views after actions
             * @var bool */
            'auto'               => true,
        
            /* @var string */
            'file_extension'     => '.phtml',
            
            /* @var bool */
            'short_tags'         => true,
            
            /* Alternative rendering engine
             * @see Atomik::renderFile()
             * @var callback */
            'engine'             => false,
            
            /* @var string */
            'default_context'    => 'html',
            
            /* The GET parameter to retrieve the current context
             * @var string */
            'context_param'      => 'format',
            
            /* List of contexts where keys are the context name.
             * Contexts can specify:
             *  - suffix (string): the view filename's extension suffix
             *  - layout (bool): whether the layout should be rendered
             *  - content_type (string): the HTTP response content type
             * @var array */
            'contexts' => array(
                'html' => array(
                    'suffix'         => '',
                    'layout'         => true,
                    'content_type'   => 'text/html'
                ),
                'ajax' => array(
                    'suffix'         => '',
                    'layout'         => false,
                    'content_type'   => 'text/html'
                ),
                'xml' => array(
                    'suffix'         => 'xml',
                    'layout'         => false,
                    'content_type'   => 'text/xml'
                ),
                'json' => array(
                    'suffix'         => 'json',
                    'layout'         => false,
                    'content_type'   => 'application/json'
                ),
                'js' => array(
                    'suffix'         => 'js',
                    'layout'         => false,
                    'content_type'   => 'text/javascript'
                ),
                'css' => array(
                    'suffix'         => 'css',
                    'layout'         => false,
                    'content_type'   => 'text/css'
                )
            )
        ),
        
        /* A parameter in the route that will allow to specify the http method 
         * (override the request's method). False to disable
         * @var string */
        'http_method_param'       => '_method',
        
        /* @var array */
        'allowed_http_methods'    => array('GET', 'POST', 'PUT', 'DELETE', 'TRACE', 'HEAD', 'OPTIONS', 'CONNECT')
        
     )       
));


/* -------------------------------------------------------------------------------------------
 *  CORE CONFIGURATION
 * ------------------------------------------------------------------------------------------ */

Atomik::set(array(

    /* @var array */
    'plugins'                    => array(),

    /* @var array */
    'atomik' => array(
    
        /* Base url, set to null for auto detection
         * @var string */
        'base_url'               => null,
        
        /* Whether url rewriting is activated on the server
         * @var bool */
        'url_rewriting'          => false,

        /* Whether to automatically allow additional params at the end of routed uris
         * @var bool */
        'auto_uri_wildcard'      => false,
    
        /* @var bool */
        'debug'                  => false,
    
        /* The GET parameter used to retreive the action
         * @var string */
        'trigger'                => 'action',

        /* Whether to register the class autoloader
         * @var bool */
        'class_autoload'         => true,
        
        /* Plugin's assets path template. 
         * %s will be replaced by the plugin's name
         * @see Atomik::pluginAsset()
         * @var string */
        'plugin_assets_tpl'      => 'app/plugins/%s/assets/',
    
        /* @var array */
        'dirs' => array(
            'public'             => '.',
            'plugins'            => array('Atomik' => __DIR__ . '/plugins', 'app/plugins'),
            'actions'            => 'app/actions',
            'views'              => 'app/views',
            'layouts'            => array('app/views', 'app/layouts'),
            'helpers'            => array('Atomik\Helpers' => __DIR__ . '/helpers', 'app/helpers'),
            'includes'           => array('Atomik' => __DIR__ . '/plugins', 'app/includes', 'app/libs'),
            'overrides'          => 'app/overrides'
        ),
    
        /* @var array */
        'files' => array(
            'index'              => 'index.php',
            'config'             => 'app/config', // without extension
            'bootstrap'          => 'app/bootstrap.php',
            'pre_dispatch'       => 'app/pre_dispatch.php',
            'post_dispatch'      => 'app/post_dispatch.php'
        )
        
    ),
    
    /* @var int */
    'start_time' => time() + microtime()
));
