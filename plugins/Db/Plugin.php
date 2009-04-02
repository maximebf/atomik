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
    	
    	/* connection string (see PDO) */
    	'dsn' 			=> false,
    	
    	/* username */
    	'username'		=> 'root',
    	
    	/* password */
    	'password'		=> '',
    
    	/* directories where models are stored */
    	'model_dirs' => './app/models',
    
    	/* default model adapter */
    	'default_model_adapter' => 'Local'
    	
    );
    
    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start($config)
    {
    	self::$config = array_merge(self::$config, $config);
    	
    	/** Atomik_Db */
    	require_once 'Atomik/Db.php';

		/* automatic connection */
		if (self::$config['dsn'] !== false) {
			$dsn = self::$config['dsn'];
			$username = self::$config['username'];
			$password = self::$config['password'];
			Atomik_Db::createInstance('default', $dsn, $username, $password);
		}
		
		/* adds model directories to php's include path */
		$includes = array();
		foreach (Atomik::path(self::$config['model_dirs'], true) as $dir) {
			$includes[] = $dir;
		}
		$includes[] = get_include_path();
		set_include_path(implode(PATH_SEPARATOR, $includes));

    	/** Atomik_Model */
		require_once 'Atomik/Model.php';
		
		/* loads the default model adapter */
		if (false && !empty(self::$config['default_model_adapter'])) {
			require_once 'Atomik/Model/Adapter/Factory.php';
			$adapter = Atomik_Model_Adapter_Factory::factory(self::$config['default_model_adapter']);
			Atomik_Model_Builder::setDefaultAdapter($adapter);
		}
		
		/* registers the db selector namespace */
		Atomik::registerSelector('db', array('DbPlugin', 'selector'));
		
		if (Atomik::isPluginLoaded('Console')) {
			ConsolePlugin::register('syncdb', array('DbPlugin', 'syncdbCommand'));
		}
    }
    
    /**
     * Backend support
     * Adds tabs
     */
    public static function onBackendStart()
    {
    	Atomik_Backend::addTab('Database', 'Db', 'index', 'right');
    	Atomik_Backend::addTab('Models', 'Db', 'models', 'right');
    }
	
	/**
	 * Atomik selector
	 *
	 * @param string $selector
	 * @param array $params
	 */
	public static function selector($selector, $params = array())
	{
	    /* checks if only a table name is used */
	    if (preg_match('/^[a-z_\-]+$/', $selector)) {
	        return Atomik_Db::findAll($selector, $params);
	    }
	    
	    return Atomik_Db::query($selector, $params);
	}
	
	/**
	 * Synchronise the database and the models
	 * 
	 * @param array $args
	 */
	public static function syncdbCommand($args)
	{
		
	}
}

