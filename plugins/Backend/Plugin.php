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

Atomik::loadPluginIfAvailable('Config');
Atomik::loadPluginIfAvailable('Auth');

/**
 * Backend plugin
 * 
 * @package Atomik
 * @subpackage Plugins
 */
class BackendPlugin
{
	/**
	 * Default configuration
	 * 
	 * @var array 
	 */
	public static $config = array(
		
		// the route needed to start the backend
		'route' => 'backend/*',
	
		'title' => 'Atomik Backend',
	
		'scripts' => array(),
	
		'styles' => array(
			'css/main.css'
		)
	
	);
	
	/**
	 * Plugin initialization
	 *
	 * @param array $config
	 * @return bool
	 */
	public static function start($config)
	{
        self::$config = array_merge(self::$config, $config);
        Atomik::set('backend', self::$config);
        Atomik::registerPluggableApplication('Backend', self::$config['route']);
        
        if (Atomik::isPluginLoaded('Auth')) {
        	AuthPlugin::addRestrictedUri(self::$config['route'], array('backend'));
        }
	}
}