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
 * Console plugin
 *
 * CLI commands to simplify administration
 * 
 * index.php command [arg1 [arg2 [...]]]
 *
 * Two builtin commands: init and generate
 *
 * @package Atomik
 * @subpackage Plugins
 */
class ConsolePlugin
{
	/** @var array */
    public static $config = array();
    
    /** @var array */
    protected static $_commands = array();
    
    /**
     * Checks we're in console mode
     *
     * @param array $config
     * @return bool
     */
    public static function start(&$config)
    {
        $config = array_merge(array(
        
        	// directory where scripts are stored
        	'scripts_dir'	=> './app/scripts'
        	
        ), $config);
        self::$config = &$config;
        
    	// checks if we are in the CLI
    	if (isset($_SERVER['HTTP_HOST'])) {
    		return false;
    	}
    	
    	// registers builtin commands
    	self::register('init', array('ConsolePlugin', 'init'));
    	self::register('generate', array('ConsolePlugin', 'generate'));
    }
    
    /**
     * Returns script directories
     * 
     * @return array
     */
    public static function getScriptDirs()
    {
		$paths = (array) self::$config['scripts_dir'];
		foreach (Atomik::getLoadedPlugins(true) as $plugin => $path) {
			$paths[] = $path;
		}
		return $paths;
    }
    
	/**
	 * Display the console and execute callbacks associated
	 * to the command
	 */
	public static function onAtomikStart()
	{
		restore_error_handler();
		$success = true;
		
		try {
			echo "Atomik " . ATOMIK_VERSION . " Console\n\n";
			
			if ($_SERVER['argc'] <= 1) {
				Atomik::fireEvent('Console::End');
				Atomik::end(true);
			}
			
			/* get parameters from the command line arguments */
			$command = $_SERVER['argv'][1];
			$arguments = array_slice($_SERVER['argv'], 2);
			
			/* console starts */
			Atomik::fireEvent('Console::Start', array(&$command, &$arguments));
			
			/* checks if a script file exists */
			if (($scriptFilename = Atomik::path($command . '.php', self::getScriptDirs())) !== false) {
				/* run the script */
				require $scriptFilename;
			} else {
				/* checks if the command is registered */
				if (!array_key_exists($command, self::$_commands)) {
					echo "The command $command does not exists\n";
					Atomik::end(true);
				}
				
				/* executes the callback */
				call_user_func(self::$_commands[$command], $arguments);
			}
			
			/* console ends */
			Atomik::fireEvent('Console::End', array($command, $arguments));
			
		} catch (Exception $e) {
	        self::_displayError($e);
			$success = false;
		}
		
		echo "\n\nDone\n";
		Atomik::end($success);
	}
	
	/**
	 * Display an error message
	 *
	 * @param Exception $e
	 */
	public static function onAtomikError($e)
	{
	    self::_displayError($e);
	    Atomik::end(false);
	}
	
	/**
	 * Display an exception
	 *
	 * @param Exception $e
	 */
	private static function _displayError(Exception $e)
	{
		self::println('AN ERROR OCCURED at line ' . $e->getLine() . ' in ' . $e->getFile() . "\n");
		self::println($e->getMessage() . "\n", 1);
		self::println($e->getTraceAsString());
	}
	
	/**
	 * Registers a callback to call when a command is
	 * executed
	 *
	 * @param string $command
	 * @param callback $callback
	 */
	public static function register($command, $callback)
	{
		self::$_commands[$command] = $callback;
	}
	
	/**
	 * Prints a message
	 *
	 * @param string $message
	 * @param int $indent OPTIONAL Indentation
	 */
	public static function println($message, $indent = 0)
	{		
		echo "\n" . str_repeat("\t", $indent) . $message;
	}
	
	/**
	 * Prints a success message
	 *
	 * @param string $message OPTIONAL
	 * @return bool TRUE
	 */
	public static function success($message = '')
	{
		echo ' [SUCCESS' . (!empty($message) ? ': ' . $message : '') . ']';
		return true;
	}
	
	/**
	 * Prints a fail message
	 *
	 * @param string $message OPTIONAL
	 * @return bool FALSE
	 */
	public static function fail($message = '')
	{
		echo ' [FAIL' . (!empty($message) ? ': ' . $message : '') . ']';
		return false;
	}
	
	/**
	 * Creates a directory
	 *
	 * @param string $dir
	 * @param int $indent OPTIONAL Indentation of the console text
	 * @param string $message OPTIONAL Message to announce the action
	 * @return bool
	 */
	public static function mkdir($dir, $indent = 0, $message = 'Creating directory: ')
	{
		self::println($message . $dir, $indent);
		
		/* checks if the file exists */
		if (file_exists($dir)) {
			if (!is_dir($dir)) {
				/* it exists but it's not a directory */
				return self::fail('Already exists and is not a directory');
			} else {
				/* it exists and it's a directory, no need to continue */
				return self::success('Already exists');
			}
		}
		
		/* creates the directory */
		if (!@mkdir($dir)) {
			return self::fail();
		}
		return self::success();
	}
	
	/**
	 * Creates a file
	 *
	 * @param string $filename
	 * @param string $content OPTIONAL File content
	 * @param int $indent OPTIONAL Indentation of the console text
	 * @param string $message OPTIONAL Message to announce the action
	 * @return bool
	 */
	public static function touch($filename, $content = '', $indent = 0, $message = 'Creating file: ')
	{
		self::println($message . $filename, $indent);
		
		/* writes the file */
		if (file_put_contents($filename, $content) === false) {
			return self::fail();
		}
		
		return self::success();
	}
	
	/**
	 * Init command
	 * index.php init [--htaccess]
	 *
	 * @param array $arguments
	 */
	public static function init($arguments)
	{
		self::println('Creating directory structure');
		
		/* checks if current directory is writeable */
		if (!is_writeable(dirname(__FILE__))) {
			return self::fail('Current directory is not writeable');
		}
		
		/* creates the actions directory */
		foreach (Atomik::path(Atomik::get('atomik/dirs/actions'), true) as $path) {
		    self::mkdir($path, 1);
		}
			
		/* creates the templates directory */
		foreach (Atomik::path(Atomik::get('atomik/dirs/views'), true) as $path) {
		    self::mkdir($path, 1);
		}
		
		/* creates the plugins directory */
		foreach (Atomik::path(Atomik::get('atomik/dirs/plugins'), true) as $path) {
		    self::mkdir($path, 1);
		}
		
		/* creates the includes directory */
		foreach (Atomik::path(Atomik::get('atomik/dirs/includes'), true) as $path) {
		    self::mkdir($path, 1);
		}
		
		/* creates the styles directory */
		self::mkdir(dirname(__FILE__) . '/styles', 1);
		
		/* creates the images directory */
		self::mkdir(dirname(__FILE__) . '/images', 1);
		
		/* fires an event so other package can do stuff too */
		Atomik::fireEvent('Console::Init', array($arguments));
		
		/* creates the .htaccess file */
		if (in_array('--htaccess', $arguments)) {
			$trigger = Atomik::get('atomik/trigger');
			$htaccess = "<IfModule mod_rewrite.c>\n\t"
			          . "RewriteEngine on\n\t"
			          . "RewriteRule ^app/plugins/(.+)/assets - [L]\n\t"
			          . "RewriteRule ^app/ - [L,F]\n\t"
			          . "RewriteCond %{REQUEST_FILENAME} !-f\n\t"
					  .	"RewriteCond %{REQUEST_FILENAME} !-d\n\t"
					  . "RewriteRule ^(.*)$ index.php?$trigger=\$1 [L,QSA]\n"
					  . "</IfModule>";
						
			self::touch(dirname(__FILE__) . '/.htaccess', $htaccess);
		}
		
		/* generate the default action scripts */
		self::generate(array(Atomik::get('atomik/default_action')));
	}
	
	/**
	 * Generate command.
	 * index.php generate action_name [action_name [action_name [...]]]
	 *
	 * @param array $arguments
	 */
	public static function generate($arguments)
	{
		foreach ($arguments as $action) {
			self::println('Generating ' . $action);
			$filename = ltrim($action, '/') . '.php';
			
			/* creates the action file */
			self::touch(Atomik::path(Atomik::get('atomik/dirs/actions')) . $filename, 
				"<?php\n\n\t/* Logic goes here */\n", 1);
		
			/* creates the template file */
			self::touch(Atomik::path(Atomik::get('atomik/dirs/views')) . $filename, '', 1);
		
			/* fires an event to allow packages to extend the generate command */
			Atomik::fireEvent('Console::Generate', array($action));
		}
	}
}
