<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
	/** @var array */
    public static $config = array();
    
    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start(&$config)
    {
    	$config = array_merge(array(
        	// connection string (see PDO)
        	'dsn' 			    => false,
        	
        	// username
        	'username'		    => 'root',
        	
        	// password
        	'password'		    => '',
        
        	// table prefix
        	'table_prefix'	    => '',
        
            // where to find sql scripts
            'sql_dirs'          => ATOMIK_APP_ROOT . '/sql',
        
            // default db instance name
            'default_instance'  => 'default',
        
            // other db instance to create
            'instances' 	    => array()
    	), $config);
    	
    	self::$config = &$config;
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
		$sql = self::dbCreateSql($instance, $filter);
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
		$sql = '';
		
		// plugins
        foreach (Atomik::getLoadedPlugins(true) as $plugin => $path) {
            if ((count($filter) && in_array($plugin, $filter)) || !count($filter)) {
	            if (@is_dir($path . '/sql')) {
	                $sql .= self::getSqlFilesFromDir($path . '/sql');
	            }
            }
        }
        
        // app
        if ((count($filter) && in_array('App', $filter)) || !count($filter)) {
            foreach ((array) self::$config['sql_dirs'] as $path) {
                if (@is_dir($path)) {
                    $sql .= self::getSqlFilesFromDir($path);
                }
            }
        }
        
		return $sql;
	}
	
	/**
	 * The console command for db-create
	 * 
	 * @param	array	$args
	 */
	public static function dbCreateCommand($args)
	{
		ConsolePlugin::println('Executing sql files');
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
		$instance = isset($args[0]) ? array_shift($args) : 'default';
		echo self::dbCreateSql($instance, $args);
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

