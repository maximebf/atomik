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
 * @subpackage Backend
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * Backend main class
 * 
 * @package Atomik
 * @subpackage Backend
 */
class Atomik_Backend
{
    /** @var string */
    private static $backendPluginPath;
    
    /** @var string */
    private static $plugin;
    
    /** @var string */
    private static $uri;
    
    /** @var Atomik_Assets */
    private static $assets;
    
    public static function bootstrap()
    {
        $loadedPlugins = Atomik::getLoadedPlugins(true);
        self::$backendPluginPath = $loadedPlugins['Backend'];
        
        $assets = new Atomik_Assets();
        $assets->setUrlFormater(array('Atomik', 'asset'));
        $assets->setBaseUrl('');
        include self::$backendPluginPath . '/Assets.php';
        self::$assets = $assets;
        
    	// backend layout
    	Atomik::set('app/layout', 'main');
    	
    	$uri = Atomik::get('request_uri');
    	if (empty($uri)) {
    		$uri = 'backend/index';
    	}
    	Atomik::fireEvent('Backend::Uri', array(&$uri));
    	Atomik::set('backend/full_request_uri', $uri);
    	
    	// extracting the plugin name from the uri
    	$segments = explode('/', trim($uri, '/'));
    	self::$plugin = strtolower(array_shift($segments));
    	self::$uri = implode('/', $segments);
    	$baseAction = Atomik::get('atomik/base_action');
    	
    	if (empty(self::$uri)) {
    		self::$uri = 'index';
    	}
    	
    	// reconfiguring
    	Atomik::set('backend/plugin', self::$plugin);
    	Atomik::set('backend/base_action', $baseAction);
    	Atomik::set('atomik/base_action', $baseAction . '/' . self::$plugin);
    	
    	Atomik::fireEvent('Backend::Start', array(self::$plugin));
    }
    
    public static function dispatch($uri = null)
    {
        if ($uri === null) {
            $uri = self::$uri;
        }
        
    	// configuration for the re-dispatch
    	$pluggAppConfig = array(
    		'pluginDir' 			=> null,
    		'rootDir'				=> 'backend',
    		'resetConfig'			=> false,
    		'overwriteDirs'			=> false,
    		'checkPluginIsLoaded' 	=> true
    	);
    	
    	if (self::$plugin == 'app') {
    		// this is the backend application for the user application, needs some reconfiguration
    		// the backend dir is searched inside the app/ directory
    		if (($pluggAppConfig['pluginDir'] = Atomik::findFile('backend', Atomik::get('atomik/dirs/app'))) === false) {
    			throw new Exception('No backend application defined in your application');
    		}
    		$pluggAppConfig['rootDir'] = '';
    		$pluggAppConfig['checkPluginIsLoaded'] = false;
    	}
    	
    	Atomik::fireEvent('Backend::Dispatch', array(self::$plugin, &$uri, &$pluggAppConfig));
        
        // dispatches the plugin application
        Atomik::dispatchPluggableApplication(self::$plugin, $uri, $pluggAppConfig);
    }
    
    /**
     * @return Atomik_Assets
     */
    public static function getAssets()
    {
        return self::$assets;
    }
}
    	
// creates the __() function if it is not defined
// this is to support i18n even if Lang is not loaded
if (!function_exists('__')) {
	function __()
	{
    	$args = func_get_args();
    	return vsprintf(array_shift($args), $args);
	}
}
