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

/** Atomik_Db */
require_once 'Atomik/Db.php';
    	
/**
 * Helpers function for handling databases
 *
 * @package Atomik
 * @subpackage Plugins
 */
class DbPlugin
{
	/**
	 * Default configuration
	 * 
	 * @var array 
	 */
    public static $config = array (
    	
    	// connection string (see PDO)
    	'dsn' 			=> false,
    	
    	// username
    	'username'		=> 'root',
    	
    	// password
    	'password'		=> '',
    
    	// table prefix
    	'table_prefix'	=> '',
    
    	// whether to enable query cache
    	'query_cache'	=> false,
    
    	// whether to enable result cache
    	'result_cache'	=> false,
    
    	// where to find models
    	'model_dirs'	=> array('./app/models'),
    
        // where to find sql scripts
        'sql_dirs'      => array('./app/sql')
    	
    );
    
    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start($config)
    {
    	self::$config = array_merge(self::$config, $config);

		// automatic connection
		if (self::$config['dsn'] !== false) {
			$dsn = self::$config['dsn'];
			$username = self::$config['username'];
			$password = self::$config['password'];
			
			$instance = Atomik_Db::createInstance('default', $dsn, $username, $password);
			$instance->setTablePrefix(self::$config['table_prefix']);
			$instance->enableQueryCache(self::$config['query_cache']);
			$instance->enableResultCache(self::$config['result_cache']);
		}
		
		// adds models directories to php's include path
		$includes = explode(PATH_SEPARATOR, get_include_path());
		foreach (Atomik::path(self::$config['model_dirs'], true) as $dir) {
			if (!in_array($dir, $includes)) {
				array_unshift($includes, $dir);
			}
		}
		set_include_path(implode(PATH_SEPARATOR, $includes));
		
		// registers the db selector namespace
		Atomik::registerSelector('db', array('DbPlugin', 'selector'));
		
		if (Atomik::isPluginLoaded('Console')) {
			ConsolePlugin::register('db-create', array('DbPlugin', 'dbCreateCommand'));
			ConsolePlugin::register('db-create-sql', array('DbPlugin', 'dbCreateSqlCommand'));
		}
    }
	
	/**
	 * Atomik selector
	 *
	 * @param string $selector
	 * @param array $params
	 */
	public static function selector($selector, $params = array())
	{
	    // checks if only a table name is used
	    if (preg_match('/^[a-z_\-]+$/', $selector)) {
	        return Atomik_Db::findAll($selector, $params);
	    }
	    
	    return Atomik_Db::query($selector, $params);
	}
    
    /**
     * Adds models folders to php's include path
     */
    public static function onAtomikStart()
    {
		$includes = explode(PATH_SEPARATOR, get_include_path());
		
		// add plugin's models folder to php's include path 
		foreach (Atomik::getLoadedPlugins(true) as $plugin => $dir) {
			if (!in_array($dir . '/models', $includes)) {
				array_unshift($includes, $dir . '/models');
			}
		}
		
		set_include_path(implode(PATH_SEPARATOR, $includes));
    }
    
    /**
     * 
     */
    public static function onBackendStart()
    {
    	Atomik_Backend::addMenu('model', 'Models', 'db/models', array(), 'right');
    	
    	// shared helpers should be accessible from all backend plugins
    	Atomik::add('atomik/dirs/helpers', dirname(__FILE__) . '/backend/helpers/shared');
    }
	
	/**
	 * Executes sql scripts for models and the ones located in the sql folder.
	 * Will look in the app folder as well as in each plugin folder.
	 * 
	 * @param 	string	$instance
	 * @param 	array	$filter
	 * @param	bool	$echo		Whether to echo or return the output from the script execution
	 * @return	string
	 */
	public static function dbCreate($instance = 'default', $filter = array(), $echo = false)
	{
		$script = self::getDbScript($filter, $echo);
		Atomik::fireEvent('Db::Create::Before', array(&$instance, $script));
		
		$script->run(Atomik_Db::getInstance($instance));
		
		Atomik::fireEvent('Db::Create::After', array($instance, $script));
		return $script->getOutputHandler()->getText();
	}
	
	/**
	 * Returns the full sql script
	 * 
	 * @param 	array	$filter
	 * @return	string
	 */
	public static function dbCreateSql($filter = array())
	{
		Atomik::fireEvent('Db::CreateSql', array(&$filter));
		return self::getDbScript($filter)->getSql();
	}
	
	/**
	 * Returns an Atomik_Db_Script object
	 * 
	 * @return Atomik_Db_Script
	 */
	public static function getDbScript($filter = array(), $echo = false)
	{
		$filter = array_map('ucfirst', $filter);
		
		require_once 'Atomik/Db/Script.php';
		require_once 'Atomik/Db/Script/Output/Text.php';
		require_once 'Atomik/Db/Script/File.php';
		
		$script = new Atomik_Db_Script();
		$script->setOutputHandler(new Atomik_Db_Script_Output_Text($echo));
		
		// plugins
        foreach (Atomik::getLoadedPlugins(true) as $plugin => $path) {
            if ((count($filter) && in_array($plugin, $filter)) || !count($filter)) {
	            if (@is_dir($path . '/models')) {
	                $script->addScripts(Atomik_Db_Script_Model::getScriptFromDir($path . '/models'));
	            }
	            if (@is_dir($path . '/sql')) {
	                $script->addScripts(Atomik_Db_Script_File::getScriptFromDir($path . '/sql'));
	            }
            }
        }
        
        // app
        if ((count($filter) && in_array('App', $filter)) || !count($filter)) {
            foreach (Atomik::path(self::$config['model_dirs'], true) as $path) {
                if (@is_dir($path)) {
                    $script->addScripts(Atomik_Db_Script_Model::getScriptFromDir($path));
                }
            }
            foreach (Atomik::path(self::$config['sql_dirs'], true) as $path) {
                if (@is_dir($path)) {
                    $script->addScripts(Atomik_Db_Script_File::getScriptFromDir($path));
                }
            }
        }
		
		Atomik::fireEvent('Db::Script', array($script, $paths));
		return $script;
	}
	
	/**
	 * The console command for db-create
	 * 
	 * @param	array	$args
	 */
	public static function dbCreateCommand($args)
	{
		$instance = isset($args[0]) ? array_shift($args) : 'default';
		self::dbCreate($instance, $args, true);
	}
	
	/**
	 * The console command for db-create-sql
	 * 
	 * @param	array	$args
	 */
	public static function dbCreateSqlCommand($args)
	{
		echo self::dbCreateSql($args);
	}
}

