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

/** Atomik_Config */
require_once 'Atomik/Config.php';

/** Atomik_Config_Backend_Factory */
require_once 'Atomik/Config/Backend/Factory.php';

/**
 * Config plugin
 * 
 * @package Atomik
 * @subpackage Plugins
 */
class ConfigPlugin
{
	/**
	 * Default configuration
	 * 
	 * @var array 
	 */
	public static $config = array(
	
		'backend'		=>	'Database',
		'backend_args'	=> 	array()
	
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
		
		$backend = Atomik_Config_Backend_Factory::factory(self::$config['backend'], self::$config['backend_args']);
		Atomik_Config::setBackend($backend);
		
		Atomik::set(Atomik_Config::getAll(), null, false);
	}
	
	/**
	 * 
	 */
	public static function onBackendStart()
	{
		Atomik_Backend::addMenu('settings', 'Settings', 'config', array(), 'right');
		Atomik_Backend::addSubMenu('settings', 'General', 'config/index');
		Atomik_Backend::addSubMenu('settings', 'Plugins', 'config/plugins');
		Atomik_Backend::addSubMenu('settings', 'Configuration', 'config/editor');
		Atomik_Backend::addSubMenu('settings', 'Backend', 'backend/settings');
	}
	
	/**
	 * 
	 */
    public static function onDbCreatesqlAfter(&$sql)
    {
    	if (self::$config['backend'] == 'Database') {
			$sql .= Atomik_Config_Backend_Database::getTableSql();
    	}
    }
}