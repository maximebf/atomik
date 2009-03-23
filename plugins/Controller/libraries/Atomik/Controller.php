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
	 * Dispatches a request to a controller action
	 *
	 * @param array $request
	 */
	public function _dispatch($request)
	{
		$this->_data = $_POST;
		$args = array();
		
		try {
			$method = new ReflectionMethod($this, $request['action']);
			if (!$method->isPublic()) {
				Atomik::trigger404();
			}
			
			$docBlock = $method->getDocComment();
			if (preg_match_all('/@route (.+)$/m', $docBlock, $matches)) {
				/* default route parameters */
				$default = array(
					'controller' => $request['controller'], 
					'action' => $request['action']
				);
				/* fetching optional parameters to the method to add them to
				 * the default array */
				foreach ($method->getParameters() as $param) {
					if ($param->isOptional()) {
						$default[$param->getName()] = $param->getDefaultValue();
					}
				}
				
				/* route base */
				$base = $request['controller'] . '/' . $request['action'] . '/';
				
				/* building routes */
				$routes = array();
				for ($i = 0, $c = count($matches[0]); $i < $c; $i++) {
					$routes[$base . $matches[1][$i]] = $default;
				}
				
				/* re-routing request */
				if (($request = Atomik::route(Atomik::get('request_uri'), $_GET, $routes)) === false) {
					Atomik::trigger404();
				}
			}
		
			/* building method parameters using request params */
			foreach ($method->getParameters() as $param) {
				if (array_key_exists($param->getName(), $request)) {
					$args[] = $request[$param->getName()];
				} else if (!$param->isOptional()) {
					throw new Exception('Missing parameter ' . $param->getName());
				}
			}
			
		} catch (Exception $e) {
			/* do not stop if __call() exist, so it allows us to trap method calls */
			if (!method_exists($this, '__call')) {
				Atomik::trigger404();
			}
		}
		
		$this->_params = $request;
		
		$this->_before();
		call_user_func_array(array($this, $request['action']), $args);
		$this->_after();
		
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
	protected function _before()
	{
		
	}
	
	/**
	 * Called after an action
	 */
	protected function _after()
	{
		
	}
}