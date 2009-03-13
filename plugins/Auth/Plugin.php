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
	 * 
	 */
    public static $config = array(
    	'roles' 			=> array(),
    	'resources' 		=> array(),
    	'map' 				=> array(),
    	'auto_map' 			=> true,
    	'guest_roles' 		=> array(),
    	'forbidden_action' 	=> false,
    	'backend' 			=> null,
    	'backend_args'		=> array()
    );
    
	/**
	 * 
	 */
    protected static $_privateUris = array();
    
	/**
	 * 
	 */
    public static function start($config = array())
    {
    	self::$config = array_merge(self::$config, $config);
    	
    	// backend
    	if (self::$config['backend'] === null) {
    		throw new Exception('A backend must be specified');
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
    	
    	// binding uris to roles
    	foreach (self::$config['map'] as $uri => $resources) {
    		self::$_privateUris[$uri] = Atomik_Auth::getResourceRoles($resources);
    	}
    	
    	if (self::$config['auto_map']) {
    		// extracting uris from resources
	    	foreach (Atomik_Auth::getAcl() as $resource => $roles) {
	    		if ($resource{0} == '/') {
	    			self::$_privateUris[$resource] = $roles;
	    		}
	    	}
    	}
    }
    
	/**
	 * 
	 */
    public static function isCurrentUriAccessible()
    {
    	$userRoles = self::$config['guest_roles'];
    	if (($user = Atomik_Auth::getCurrentUser()) !== null) {
    		$userRoles = $user->getRoles();
    	}
    	
    	foreach (self::$_privateUris as $uri => $roles) {
    		if (Atomik::uriMatch($uri)) {
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
    public static function onAtomikDispatchBefore(&$cancel)
    {
    	if (!self::isCurrentUriAccessible()) {
    		if (self::$config['forbidden_action'] !== false) {
    			Atomik::redirect(Atomik::url(self::$config['forbidden_action'], array('from' => A('request_uri'))), false);
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