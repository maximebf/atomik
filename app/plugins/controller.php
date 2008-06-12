<?php
/**
 * Atomik Framework
 * 
 * @package Atomik
 * @subpackage Controller
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */
 
/* default configuration */
Atomik::setDefault(array(
    'controller' => array(

    	/* version */
    	'version' 	=> '1.1'
	
    )
));

/**
 * Controller plugin
 *
 * When enable, actions files becomes controllers. Also implements
 * a router.
 *
 * URI:
 *
 * The default behaviour of Atomik is to execute the script which
 * have the same name as the uri. For example, calling /index will
 * execute the script index.php. With this plugin, this
 * is still true but to some extend only. Calling /index will load
 * index.php from the actions directory but it will execute the index method of 
 * the class named IndexController. Calling /index/add will execute
 * the add method of the IndexController class. Methods are called actions.
 * The basic idea is /controller/action
 *
 * CONTROLLER:
 *
 * A controller is a class. The class name must be the controller name
 * with a capital letter suffixed with the word Controller. For example,
 * the index controller class would be named IndexController.
 * Methods of this class are called actions. The action specified in the
 * uri will be called.
 * Example:
 *
 * class IndexController
 * {
 *    public function index()
 *    {
 *    }
 * }
 *
 * Action methods can have one argument which is an array containing the
 * route parameters (see below). If you don't want one of your method to be
 * accessible from the url, prefixed it with an underscore.
 *
 * The template script is thus not the same as usual. Indeed, keeping
 * the same script would mean all actions share it. Template scripts,
 * called views, are stored in a directory named like the controller inside
 * the templates folder. For example, the view for the add action of
 * the index controller is stored in templates/index/add.php
 *
 * Views variables are also not set the same way. It is obvious that
 * it's not possible to do $var = 'value' inside a controller action and then
 * access $var inside the view. To set a view variable, set it as an object
 * property: $this->var = 'value'. All controller's properties will be accessible
 * from the view like usual (i.e. $var). If you don't want one of your properties
 * to be exported prefixed it with and underscore.
 *
 * Finally, you can define the _beforeAction() or _afterAction() methods in your
 * controller. They will be called respectively before and after all actions.
 *
 * ROUTER
 *
 * It is possible to modify the way url are handled using the router. A route is
 * basically an url. For example /index/add is a route. It is then needed to map
 * this route to the correct controller and action.
 * Routes are defined inside the controller_routes config key - which is an array.
 * For example to map the route /index/add to the index controller and the add action:
 *
 * array(
 *    'index/add' => array(
 *        'controller' => 'index',
 *        'action' => 'add'
 *    )
 * )
 *
 * As you see, the route is define as the array key and its parameters are defined in
 * the sub array. You can add an unlimited numbers of parameters to the route. There
 * must be at least the controller and action parameters for the route to be valid.
 *
 * The real magic of the routes is the possibility to assign a parameter value with
 * a segment of the uri. This is done by specifying inside the route a parameter name
 * prefixed with ":". For example, the default route is:
 *
 * array(
 *    ':controller/:action' => array(
 *        'controller' => 'index',
 *        'action' => 'index'
 *    )
 * )
 *
 * If the uri is /index/add, :controller will be replace with index and :action with
 * add. The controller and action parameters are still defined inside the parameters
 * array as default value. Thus the controller and the action are optional. So, for
 * example /blog will match the blog controller and the index action. If no default
 * value are specified, the uri segment must be specified for the route to be valid.
 * Example:
 *
 * array(
 *    'archives/:year/:month' => array(
 *        'controller' => 'archives',
 *        'action' => 'view',
 *        'year' => 2008
 *    )
 * )
 *
 * Will match /archives/2008/12 but won't match /archives/2008 (the month parameter
 * is not optional and is not defined).
 *
 * If the url contains more segments than the route specified, they will be transformed
 * as parameters. Example, using the default route:
 *
 *  /index/add/key1/value1/key2/value2
 *
 * The route parameters will be:
 *
 * array(
 *     'controller' => 'index',
 *      'action' => 'add',
 *      'key1' => 'value1',
 *      'key2' => 'value2'
 * )
 *
 * Routes are match in reverse order.
 *
 * @package Atomik
 * @subpackage Controller
 */
class ControllerPlugin
{
	/**
	 * Rewrite the url and build the request
	 */
	public static function onAtomikDispatchBefore()
	{
		Atomik::fireEvent('Controller::Router::Start');
		
		/* retreives routes */
		$routes = array_reverse(array_merge(array(
			/* default route */
			':controller/:action/:id' => array(
				'controller' => 'index',
				'action' => 'index',
				'id' => null
			)
		), Atomik::get('controller/routes', array())), true);
		
		/* retreives the url */
		$uri = trim(Atomik::get('request'), '/');
		$uriSegments = explode('/', $uri);
		
		/* searches for a route matching the uri */
		$found = false;
		$request = array();
		foreach ($routes as $route => $default) {
			$valid = true;
			$segments = explode('/', trim($route, '/'));
			$request = $default;
			
			for ($i = 0, $count = count($segments); $i < $count; $i++) {
				if (substr($segments[$i], 0, 1) == ':') {
					/* segment is a parameter */
					if (isset($uriSegments[$i])) {
						/* this segment is defined in the uri */
						$request[substr($segments[$i], 1)] = $uriSegments[$i];
						$segments[$i] = $uriSegments[$i];
					} else if (!array_key_exists(substr($segments[$i], 1), $default)) {
						/* not defined in the uri and no default value */
						$valid = false;
						break;
					}
				} else {
					/* fixed segment */
					if (!isset($uriSegments[$i]) || $uriSegments[$i] != $segments[$i]) {
						$valid = false;
						break;
					}
				}
			}
			
			/* checks if route is valid and if controller and action params are set */
			if ($valid && isset($request['controller']) && isset($request['action'])) {
				$found = true;
				/* if there's remaining segments in the uri, adding them as params */
				if (($count = count($uriSegments)) > ($start = count($segments))) {
					for ($i = $start; $i < $count; $i += 2) {
						if (isset($uriSegments[$i + 1])) {
							$request[$uriSegments[$i]] = $uriSegments[$i + 1];
						}
					}
				}
				break;
			}
		}
		
		if (!$found) {
			/* route not found */
			Atomik::trigger404();
		}
		
		/* saves the request */
		Atomik::set('controller/request', $request);
		
		Atomik::fireEvent('Controller::Router::End');
	}
	
	/**
	 * Changes the action name for atomik to find the file
	 */
	public static function onAtomikExecuteStart(&$action)
	{
		$action = Atomik::get('controller/request/controller');
	}
	
	/**
	 * Dispatch the request to the controller action
	 */
	public static function onAtomikExecuteAfter($action, &$template, &$vars, &$render, &$echo, &$triggerError)
	{
		Atomik::fireEvent('Controller::Dispatch::Before', array(&$template, &$vars));
		$request = Atomik::get('controller/request');
		
		/* checks if the action starts with an underscore */
		if (substr($request['action'], 0, 1) == '_') {
			Atomik::trigger404();
		}
		
		/* controller class name */
		$classname = ucfirst(strtolower($request['controller'])) . 'Controller';
		
		/* checks if the controller class exists */
		if (!class_exists($classname)) {
			throw new Exception('Controller ' . $classname . ' not found');
			return;
		}
		
		/* creates the controller instance */
		$instance = new $classname();
		if (!method_exists($instance, $request['action'])) {
			Atomik::trigger404();
		}
		
		Atomik::fireEvent('Controller::Action::Before', array(&$request, &$instance));
		
		/* executes beforeAction if it exists */
		if (method_exists($instance, '_before')) {
			$instance->_before(&$request);
		}
		
		/* call the method named like the action with the request as unique argument */
		call_user_func(array($instance, $request['action']), $request);
		
		/* executes afterAction if it exists */
		if (method_exists($instance, '_after')) {
			$instance->_after($request);
		}
		
		Atomik::fireEvent('Controller::Action::After', array(&$request, &$instance));
		
		/* gets the instance properties and sets them in the global scope for the view */
		$vars = array();
		foreach (get_object_vars($instance) as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$vars[$name] = $value;
			}
		}
		
		/* override request_template with the view filename */
		$template = $request['controller'] . '/' . $request['action'];
		
		Atomik::fireEvent('Controller::Dispatch::After', array($instance, &$template, &$vars));
	}
	
	/**
	 * Overrides default generator behaviour
	 */
	public static function onConsoleGenerate($action)
	{
		ConsolePlugin::println('Generating controller structure');
		
		/* adds a class definition inside the action file */
		$filename = Atomik::get('atomik/paths/actions') . $action . '.php';
		ConsolePlugin::touch($filename, "<?php\n\nclass " . ucfirst($action) . "Controller\n{\n}\n", 1);
		
		/* removes the presentation file and replaces it with a directory */
		$templatePath = Atomik::get('atomik/paths/templates');
		@unlink($templatePath . $action . '.php');
		ConsolePlugin::mkdir($templatePath . $action, 1);
	}
}

