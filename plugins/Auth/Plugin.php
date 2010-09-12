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
	/** @var array */
    public static $config = array();
    
	/** @var array */
    private static $_restrictedUris = array();
    
	/**
	 * @param array $config
	 */
    public static function start(&$config)
    {
    	$config = array_merge(array(
            // the route from which the pluggable app will be accessble
            // false to disable
        	'route'				=> 'auth*',
        
            // the Atomik_Model class name that represents a user
        	'model' 			=> null,
        
            // the username field in the model
            'model_user_field'  => 'username',
        
            // the password field in the model
            'model_pass_field'  => 'password',
        
            // if model is null, an array of users
            // for Atomik_Auth_Backend_Array
        	'users'				=> null,
        
            // an array of roles for Atomik_Auth
        	'roles' 			=> array(),
        
            // an array of resources for Atomik_Auth
        	'resources' 		=> array(),
        
            // restricted uris (array in the same form as resources)
        	'restricted' 		=> array(),
        
            // roles for unauthentified users
        	'guest_roles' 		=> array(),
        
            // whether to treat resources that starts with
            // a slash as restricted uris
            'restricted_uris_from_resources' => true,
        
            // the action to go to when the user is not logged in
        	'login_action' 	    => 'auth/login',
        
            // additional Atomik_Auth backend in the form of
            // array('name' => 'backend class name', 'args' => array('constructor args'))
        	'backends' 			=> array()
        ), $config);
    	self::$config = &$config;
    	
    	if (self::$config['route'] !== false) {
    		Atomik::registerPluggableApplication('Auth', 
    		    self::$config['route'], array('overwriteDirs' => false));
    	}
    	
    	if (self::$config['model'] !== null) {
    		Atomik::loadPlugin('Db');
    		
    		$backend = new Atomik_Auth_Backend_Model(
    		    self::$config['model'], 
    		    self::$config['model_user_field'], 
    		    self::$config['model_pass_field']
		    );
    		        
    		Atomik_Auth::addBackend($backend);
    		Atomik_Auth::setLocator($backend->getLocator());
    		
    	} else if (self::$config['users'] !== null) {
    		$backend = new Atomik_Auth_Backend_Array(self::$config['users']);
    		Atomik_Auth::addBackend($backend);
    		Atomik_Auth::setLocator($backend->getLocator());
    	}
    	
    	// backends
    	foreach (self::$config['backends'] as $backendInfo) {
	    	$backend = Atomik_Auth_Backend_Factory::factory(
	    	            $backendInfo['name'], $backendInfo['args']);
	    	Atomik_Auth::addBackend($backend);
    	}
    	
    	// roles and resources
    	Atomik_Auth::setRoles(self::$config['roles']);
    	Atomik_Auth::setResources(self::$config['resources']);
    	
    	// restricted uris
    	foreach (self::$config['restricted'] as $uri => $roles) {
    	    if (is_int($uri)) {
    	        $uri = $roles;
    	        $roles = array();
    	    }
    	    self::addRestrictedUri($uri, $roles);
    	}
    	
    	// extracting uris from resources
    	if (self::$config['restricted_uris_from_resources']) {
        	foreach (Atomik_Auth::getAcl() as $resource => $roles) {
        		if ($resource{0} == '/') {
        			self::addRestrictedUri($resource, $roles);
        		}
        	}
    	}
    }
    
    /**
     * @param string $uri
     * @param array $roles
     */
    public static function addRestrictedUri($uri, $roles = array())
    {
    	self::$_restrictedUris[trim($uri, '/')] = (array) $roles;
    }
    
    /**
     * @return array
     */
    public static function getRestrictedUris()
    {
    	return self::$_restrictedUris;
    }
    
	/**
	 * @return bool
	 */
    public static function isCurrentUriAccessible()
    {
    	$requestUri = Atomik::get('full_request_uri');
    	$userRoles = self::$config['guest_roles'];
    	
    	if (Atomik_Auth::getLocator() !== null && 
    	    ($user = Atomik_Auth::getCurrentUser()) !== null && 
    	        $user instanceof Atomik_Auth_User_Interface) {
    		        $userRoles = $user->getRoles();
    	}
    	
    	foreach (self::$_restrictedUris as $uri => $roles) {
    		if (Atomik::uriMatch($uri, $requestUri)) {
    		    if (!Atomik_Auth::isLoggedIn()) {
    		        return false;
    		    }
    			foreach ($roles as $role) {
    				if (!Atomik_Auth::isAllowed($role, $userRoles)) {
    					return false;
    				}
    			}
    			break;
    		}
    	}
    	
    	return true;
    }
    
    /* ------------------------------------------------------------------------
     * Events handlers
     */
    
    public static function onAtomikDispatchUri(&$uri, &$request, &$cancel)
    {
    	if (!self::isCurrentUriAccessible()) {
    		if (self::$config['login_action'] !== false) {
    			Atomik::redirect(Atomik::appUrl(self::$config['login_action'], 
    			    array('from' => Atomik::appUrl(Atomik::get('full_request_uri')))), false);
    		} else {
    			Atomik::trigger404();
    		}
    	}
    	Atomik::fireEvent('Auth::Check');
    }
    
    public static function onBackendStart()
    {
    	//Atomik_Backend::addMenu('users', 'Users', 'auth', array(), 'right');
    }
    
    public static function onDbCreatesqlExporter($exporter)
    {
    	if (self::$config['model'] !== null) {
    		if (self::$config['model'] == 'Atomik_Auth_User_Model') {
    			$exporter->addDescriptor(Atomik_Model_Descriptor::factory('Atomik_Auth_User_Model'));
    		}
    	}
    }
}