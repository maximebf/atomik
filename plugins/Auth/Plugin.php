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
 * @package Atomik
 * @subpackage Plugins
 */
class AuthPlugin
{
	/**
	 * @var array
	 */
    public static $config = array(
    	'route'				=> 'auth/*',
    	'roles' 			=> array(),
    	'resources' 		=> array(),
    	'guest_roles' 		=> array(),
    	'forbidden_action' 	=> 'auth/login',
    	'backend' 			=> null,
    	'backend_args'		=> array()
    );
    
	/**
	 * @var array
	 */
    protected static $_privateUris = array();
    
	/**
	 * Starts the plugin
	 * 
	 * @param array $config
	 */
    public static function start($config = array())
    {
    	self::$config = array_merge(self::$config, $config);
    	
    	if (self::$config['route'] !== false) {
    		Atomik::registerPluggableApplication('Auth', self::$config['route'], array('overwriteDirs' => false));
    	}
    	
    	// backend
    	if (self::$config['backend'] === null) {
    		self::$config['backend'] = 'Array';
    		self::$config['backend_args'] = array(Atomik::get('users', array(), self::$config));
    	}
    	
    	$classname = 'Atomik_Auth_Backend_' . self::$config['backend'];
    	if (!class_exists($classname)) {
    		$classname = self::$config['backend'];
    		if (!class_exists($classname)) {
    			throw new Exception('The backend ' . $classname . ' cannot be found');
    		}
    	}
    	$class = new ReflectionClass($classname);
    	$backend = $class->newInstanceArgs(self::$config['backend_args']);
    	Atomik_Auth::setBackend($backend);
    	
    	// roles and resources
    	Atomik_Auth::setRoles(self::$config['roles']);
    	Atomik_Auth::setResources(self::$config['resources']);
    	
    	// extracting uris from resources
    	foreach (Atomik_Auth::getAcl() as $resource => $roles) {
    		if ($resource{0} == '/') {
    			self::$_privateUris[$resource] = $roles;
    		}
    	}
    }
    
	/**
	 * Checks if the request uri is accessible to the currently logged in user
	 * 
	 * @return bool
	 */
    public static function isCurrentUriAccessible()
    {
    	$requestUri = Atomik::get('full_request_uri');
    	$userRoles = self::$config['guest_roles'];
    	if (($user = Atomik_Auth::getCurrentUser()) !== null) {
    		$userRoles = $user->getRoles();
    	}
    	
    	foreach (self::$_privateUris as $uri => $roles) {
    		if (Atomik::uriMatch($uri, $requestUri)) {
    			foreach ($roles as $role) {
    				if (!Atomik_Auth::checkRole($role, $userRoles)) {
    					return false;
    				}
    			}
    		}
    	}
    	
    	return true;
    }
    
	/**
	 * 
	 */
    public static function onAtomikDispatchUri(&$uri, &$request, &$cancel)
    {
    	if (!self::isCurrentUriAccessible()) {
    		if (self::$config['forbidden_action'] !== false) {
    			if (Atomik_Auth::isLoggedIn()) {
    				Atomik::flash('Your roles do not allow you to access this section of the site', 'error');
    			}
    			Atomik::redirect(Atomik::url(self::$config['forbidden_action'], array('from' => Atomik::get('full_request_uri'))), false);
    		} else {
    			Atomik::trigger404();
    		}
    	}
    }
    
	/**
	 * 
	 */
    public static function onBackendStart()
    {
        Atomik_Backend::addTab('Users', 'Auth', 'index', 'right');
    }
}