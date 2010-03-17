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
    	'model' 			=> null,
    	'users'				=> array(),
    	'user_locator'		=> null,
    	'roles' 			=> array(),
    	'resources' 		=> array(),
    	'guest_roles' 		=> array(),
    	'forbidden_action' 	=> 'auth/login',
    	'backends' 			=> array()
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
    	
    	if (self::$config['model'] !== null) {
    		// using a model
    		Atomik::loadPlugin('Db');
    		Atomik_Auth_User_Locator_Model::setModelName(self::$config['model']);
    		Atomik_Auth::addBackend(new Atomik_Auth_Backend_Model(self::$config['model']));
    		if (self::$config['user_locator'] == 'model' || self::$config['user_locator'] == null) {
    			Atomik_Auth::setUserLocator('Atomik_Auth_User_Locator_Model');
    		}
    	}
    	
    	if (self::$config['users'] !== null) {
    		// the users array backend
    		Atomik_Auth_User_Array::setUsers(self::$config['users']);
    		Atomik_Auth::addBackend(Atomik_Auth_User_Array::getBackend());
    		if (self::$config['user_locator'] == 'array'  || self::$config['model'] === null) {
    			Atomik_Auth::setUserLocator('Atomik_Auth_User_Array');
    		}
    	}
    	
    	// backends
    	foreach (self::$config['backends'] as $backendInfo) {
	    	$backend = Atomik_Auth_Backend_Factory::factory($backendInfo['name'], $backendInfo['args']);
	    	Atomik_Auth::addBackend($backend);
    	}
    	
    	// roles and resources
    	Atomik_Auth::setRoles(self::$config['roles']);
    	Atomik_Auth::setResources(self::$config['resources']);
    	
    	// extracting uris from resources
    	foreach (Atomik_Auth::getAcl() as $resource => $roles) {
    		if ($resource{0} == '/') {
    			self::addRestrictedUri($resource, $roles);
    		}
    	}
    }
    
    /**
     * Adds a restricted URI
     * 
     * @param	string	$uri
     * @param	array	$roles
     */
    public static function addRestrictedUri($uri, $roles = array())
    {
    	self::$_privateUris[ltrim($uri, '/')] = (array) $roles;
    }
    
    public static function getRestrictedUris()
    {
    	return self::$_privateUris;
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
    				if (!Atomik_Auth::isAllowed($role, $userRoles)) {
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
    			Atomik::appRedirect(Atomik::appUrl(self::$config['forbidden_action'], array('from' => Atomik::appUrl(Atomik::get('full_request_uri')))), false);
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
    	Atomik_Backend::addMenu('users', 'Users', 'auth', array(), 'right');
    }
    
    /**
     * 
     */
    public static function onDbScript($script)
    {
    	if (self::$config['model'] !== null) {
    		if (self::$config['model'] == 'Atomik_Auth_User') {
    			$script->addScript(new Atomik_Db_Script_Model(
    				Atomik_Model_Descriptor_Factory::get('Atomik_Auth_User')));
    		}
    	}
    }
}