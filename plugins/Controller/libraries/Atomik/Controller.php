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
	/**
	 * Request parameters
	 *
	 * @var array
	 */
	protected $_params;
	
	/**
	 * POST data
	 *
	 * @var array
	 */
	protected $_data;
	
	/**
	 * @var Atomik
	 */
	protected $_helpers;
	
	public function __construct()
	{
	    $this->_helpers = Atomik::instance();
	    $this->init();
	}
	
	public function init()
	{
	
	}
	
	/**
	 * Dispatches a request to a controller action
	 *
	 * @param array $request
	 */
	public function _dispatch($action, $method, $vars)
	{
		$this->_data = array_merge($_POST, $vars);
		$this->_params = Atomik::get('request');
		$args = array();
		
		try {
		    $methodName = $action . 'Action';
			$method = new ReflectionMethod($this, $methodName);
			if (!$method->isPublic()) {
				Atomik::trigger404();
			}
		
			/* building method parameters using request params */
			foreach ($method->getParameters() as $param) {
				if (array_key_exists($param->getName(), $this->_params)) {
					$args[] = $this->_params[$param->getName()];
				} else if (!$param->isOptional()) {
					throw new Exception('Missing parameter ' . $param->getName());
				}
			}
			
		} catch (Exception $e) {
			/* do not stop if __call() exist, so it allows us to trap method calls */
			if (!method_exists($this, '__call')) {
				throw new Atomik_Exception('Missing action method in ' . get_class($this));
			}
		}
		
		$this->preDispatch();
		call_user_func_array(array($this, $methodName), $args);
		$this->postDispatch();
		
		/* gets the instance properties and sets them in the global scope for the view */
		$vars = array();
		foreach (get_object_vars($this) as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$vars[$name] = $value;
			}
		}
		return $vars;
	}
	
	/**
	 * Called before an action
	 */
	public function preDispatch()
	{
		
	}
	
	/**
	 * Called after an action
	 */
	public function postDispatch()
	{
		
	}
	
	protected function _setView($view)
	{
	    Atomik::setView($view);
	}
	
	protected function _noRender()
	{
	    Atomik::noRender();
	}
	
	protected function _setLayout($layout)
	{
	    Atomik::set('app/layout', $layout);
	}
	
	protected function _addLayout($layout)
	{
	    Atomik::add('app/layout', $layout);
	}
	
	protected function _redirect($url)
	{
	    Atomik::redirect($url);
	}
}
