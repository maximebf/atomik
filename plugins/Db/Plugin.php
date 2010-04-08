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
    
    	// where to find models
    	'model_dirs'	=> array('./app/models'),
    
        // where to find sql scripts
        'sql_dirs'      => array('./app/sql'),
    
        // default db instance name
        'default_instance' => 'default',
    
        // other db instance to create
        'instances' 	=> array()
    	
    );
    
    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start($config)
    {
    	self::$config = array_merge(self::$config, $config);
    	
		Atomik::add('atomik/dirs/helpers', dirname(__FILE__) . '/helpers');
		
		$instances = self::$config['instances'];
		$instances['default'] = array(
		    'dsn' => self::$config['dsn'],
		    'username' => self::$config['username'],
		    'password' => self::$config['password'],
		    'table_prefix' => self::$config['table_prefix']
		);
		
		// initializes instances
		foreach ($instances as $name => $instanceConf) {
    		if ($instanceConf['dsn'] !== false) {
    		    self::createInstance($name, $instanceConf);
    		}
		}
		Atomik_Db::setInstance(self::$config['default_instance']);
		
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
     * Creates a db instance from an array of info
     * 
     * @param string $name
     * @param array $instanceConf
     * @return Atomik_Db_Instance
     */
    public static function createInstance($name, $instanceConf)
    {
        $dsn = $instanceConf['dsn'];
        $username = Atomik::get('username', 'root', $instanceConf);
        $password = Atomik::get('password', '', $instanceConf);
        
		$instance = Atomik_Db::createInstance($name, $dsn, $username, $password);
		$instance->setTablePrefix(Atomik::get('table_prefix', '', $instanceConf));
		
		return $instance;
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
	public static function dbCreate($instance = 'default', $filter = array())
	{
		$sql = self::dbCreateSql($instance, $filter, $echo);
		$db = Atomik_Db::getInstance($instance);
		
		Atomik::fireEvent('Db::Create::Before', array($db, &$sql));
		
		$db->beginTransaction();
		try {
			$stmt = $db->prepare($sql);
			if (!$stmt->execute()) {
			    $info = $stmt->errorInfo();
				throw new Atomik_Db_Exception($info[2]);
			}
			$db->commit();
		} catch (Exception $e) {
			$db->rollback();
		    throw $e;
		}
		
		Atomik::fireEvent('Db::Create::After', array($db, $sql));
	}
	
	/**
	 * Returns the full sql script
	 * 
	 * @param 	array	$filter
	 * @return	string
	 */
	public static function dbCreateSql($instance = 'default', $filter = array())
	{
		$db = Atomik_Db::getInstance($instance);
		
		Atomik::fireEvent('Db::Createsql::Before', array($db, &$filter));
		$filter = array_map('ucfirst', $filter);
		
		require_once 'Atomik/Model/Exporter.php';
		$exporter = new Atomik_Model_Exporter($db);
		$sql = '';
		
		// plugins
        foreach (Atomik::getLoadedPlugins(true) as $plugin => $path) {
            if ((count($filter) && in_array($plugin, $filter)) || !count($filter)) {
	            if (@is_dir($path . '/models')) {
	                $exporter->addDescriptors(self::getDescriptorsFromDir($path . '/models'));
	            }
	            if (@is_dir($path . '/sql')) {
	                $sql .= self::getSqlFilesFromDir($path . '/sql');
	            }
            }
        }
        
        // app
        if ((count($filter) && in_array('App', $filter)) || !count($filter)) {
            foreach (Atomik::path(self::$config['model_dirs'], true) as $path) {
                if (@is_dir($path)) {
	                $exporter->addDescriptors(self::getDescriptorsFromDir($path));
                }
            }
            foreach (Atomik::path(self::$config['sql_dirs'], true) as $path) {
                if (@is_dir($path)) {
                    $sql .= self::getSqlFilesFromDir($path);
                }
            }
        }
        
        Atomik::fireEvent('Db::Createsql::Exporter', array($exporter));
        
        $sql = $exporter->getSql() . $sql;
		
		Atomik::fireEvent('Db::Createsql::After', array(&$sql));
		return $sql;
	}
	
	/**
	 * The console command for db-create
	 * 
	 * @param	array	$args
	 */
	public static function dbCreateCommand($args)
	{
		ConsolePlugin::println('Creating tables from models and executing sql files');
	    try {
    		$instance = isset($args[0]) ? array_shift($args) : 'default';
    		self::dbCreate($instance, $args, true);
    		ConsolePlugin::success();
	    } catch (Exception $e) {
	        ConsolePlugin::fail($e->getMessage());
	    }
	}
	
	/**
	 * The console command for db-create-sql
	 * 
	 * @param	array	$args
	 */
	public static function dbCreateSqlCommand($args)
	{
		ConsolePlugin::println('Generating sql from models');
		$instance = isset($args[0]) ? array_shift($args) : 'default';
		echo self::dbCreateSql($instance, $args);
	}
	
	/**
	 * Returns an array of model descriptors from the specified directory
	 * 
	 * @param string $dir
	 * @param string $parent
	 * @return array
	 */
	public static function getDescriptorsFromDir($dir, $parent = '')
	{
		$descriptors = array();
		
		foreach (new DirectoryIterator($dir) as $file) {
			if ($file->isDot() || substr($file->getFilename(), 0, 1) == '.') {
				continue;
			}
			
			$filename = $file->getFilename();
			if (strpos($filename, '.') !== false) {
				$filename = substr($filename, 0, strrpos($filename, '.'));
			}
			$className = trim($parent . '_' . $filename, '_');
			
			if ($file->isDir()) {
				$descriptors = array_merge(
				    $descriptors, 
				    self::getDescriptorsFromDir($file->getPathname(), $className)
				);
				continue;
			}
			
			require_once $file->getPathname();
			if (!class_exists($className, false) || !is_subclass_of($className, 'Atomik_Model')) {
				continue;
			}
			
			$descriptors[] = Atomik_Model_Descriptor::factory($className);
		}
		
		return $descriptors;
	}
	
	/**
	 * Returns contents of all files from the specified directory
	 * 
	 * @param string $dir
	 * @return string
	 */
	public static function getSqlFilesFromDir($dir)
	{
		$sql = '';
		
		foreach (new DirectoryIterator($dir) as $file) {
			if ($file->isDot() || substr($file->getFilename(), 0, 1) == '.') {
				continue;
			}
			
			if ($file->isDir()) {
				$sql .= self::getSqlFilesFromDir($file->getPathname());
				continue;
			}
			
			$sql .= file_get_contents($file->getPathname());
		}
		
		return $sql;
	}
}

