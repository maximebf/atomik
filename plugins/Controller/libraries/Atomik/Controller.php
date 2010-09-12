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
 * @subpackage Controller
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * @package Atomik
 * @subpackage Controller
 */
class Atomik_Controller
{
	/** @var string */
	protected $_action;
	
	/** @var array */
	protected $_params;
	
	/** @var array */
	protected $_data;
	
	/** @var string */
	protected $_httpMethod;
	
	/** @var Atomik */
	protected $_helpers;
	
	public function __construct()
	{
	    $this->_helpers = Atomik::instance();
	    $this->init();
	}
	
	/**
	 * Called after __construct()
	 */
	protected function init() {}
	
	/**
	 * Called before an action
	 */
	protected function preDispatch() {}
	
	/**
	 * Called after an action
	 */
	protected function postDispatch() {}
	
	/**
	 * Dispatches a request to a controller action
	 *
	 * @param string $action
	 * @param string $httpMethod
	 * @param array $vars
	 */
	public function _dispatch($action, $httpMethod, $vars = array())
	{
	    $this->_action = $action;
		$this->_data = $_POST;
		$this->_params = array_merge(Atomik::get('request'), $vars);
		$this->_httpMethod = $httpMethod;
		$args = array();
	    $methodName = str_replace(' ', '', ucwords(str_replace('_', ' ', $action)));
	    $methodName{0} = strtolower($methodName{0});
		
		try {
			$method = new ReflectionMethod($this, $methodName);
			if (!$method->isPublic()) {
			    if (ControllerPlugin::$config['controller_must_exists']) {
				    throw new Atomik_HttpException("'" . get_class($this) . "::$methodName()' is not a public method", 404);
			    }
			    return false;
			}
			
			// building method parameters using request params
			foreach ($method->getParameters() as $param) {
				if (array_key_exists($param->getName(), $this->_params)) {
					$args[] = $this->_params[$param->getName()];
				} else if (!$param->isOptional()) {
					throw new Atomik_Exception("Missing parameter '" . $param->getName() . "' in '" .
					    get_class($this) . "::$methodName()'");
				} else {
				    $args[] = $param->getDefaultValue();
				}
			}
			
		} catch (ReflectionException $e) {
			// do not stop if __call() exist, so it allows us to trap method calls
			if (!method_exists($this, '__call')) {
			    if (ControllerPlugin::$config['controller_must_exists']) {
				    throw new Atomik_HttpException("Method '" . get_class($this) . "::$methodName()' not found", 404);
			    }
			    return false;
			}
		}
		
		$this->preDispatch();
		call_user_func_array(array($this, $methodName), $args);
		$this->postDispatch();
	}
	
	/**
	 * Forward the current action to another action from the same controller
	 * 
	 * @param string $action
	 * @param array $params
	 */
	protected function _forward($action, $params = array())
	{
	    $className = ltrim(substr(get_class($this), 0, -10), '\\');
	    $className = substr($className, strlen(ltrim(ControllerPlugin::$config['default_namespace'], '\\')));
	    $view = trim(str_replace('\\', '/', strtolower($className)) . '/' . $action, '/');
	    
	    $this->_setView($view);
	    return $this->_dispatch(basename($action), $this->_httpMethod, $params);
	}
	
	// ------------------------------------------------------------------------------
	// Shortcut methods
	
	protected function _setView($view)
	{
	    Atomik::setView($view);
	}
	
	protected function _trigger404($message = 'Not found')
	{
	    Atomik::trigger404($message);
	}
	
	protected function _noRender()
	{
	    Atomik::noRender();
	}
	
	protected function _hasParam($name)
	{
	    return Atomik::has($name, $this->_params);
	}
	
	protected function _getParam($name, $default = null)
	{
	    return Atomik::get($name, $default, $this->_params);
	}
	
	protected function _setLayout($layout)
	{
	    Atomik::set('app/layout', $layout);
	}
	
	protected function _redirect($url, $useUrl = true, $httpCode = 302)
	{
	    Atomik::redirect($url, $useUrl, $httpCode);
	}
	
	protected function _flash($message, $label = 'default')
	{
	    Atomik::flash($message, $label);
	}
	
	protected function _isPost()
	{
	    return Atomik::get('app/http_method') == 'POST';
	}
	
	protected function _setHeader($name, $value)
	{
	    header("$name: $value");
	}
}
