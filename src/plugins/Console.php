<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Atomik;
use Atomik,
	Exception,
    AtomikException;

class Console
{
    /** @var array */
    public static $config = array();
    
    /** @var array */
    private static $commands = array();
    
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
            'scripts_dir'    => 'scripts'
            
        ), $config);
        self::$config = &$config;
        
        // checks if we are in the CLI
        if (PHP_SAPI !== 'cli') {
            return false;
        }
        
        // registers builtin commands
        self::register('init', array('Atomik\Console', 'init'));
        self::register('generate', array('Atomik\Console', 'generate'));
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
            $paths[] = "$path/scripts";
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
            
            $command = $_SERVER['argv'][1];
            $arguments = array_slice($_SERVER['argv'], 2);
            
            Atomik::fireEvent('Console::Start', array(&$command, &$arguments));
            
            if (array_key_exists($command, self::$commands)) {
                call_user_func(self::$commands[$command], $arguments);

            } else if (($scriptFilename = Atomik::findFile("$command.php", self::getScriptDirs())) !== false) {
                include $scriptFilename;

            } else {
                echo "The command $command does not exists\n";
                Atomik::end(true);
            }
            
            Atomik::fireEvent('Console::End', array($command, $arguments));
            
        } catch (Exception $e) {
            self::displayError($e);
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
        self::displayError($e);
        Atomik::end(false);
    }
    
    /**
     * Display an exception
     *
     * @param Exception $e
     */
    private static function displayError(Exception $e)
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
        self::$commands[$command] = $callback;
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
        
        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                return self::fail('Already exists and is not a directory');
            } else {
                return self::success('Already exists');
            }
        }
        
        if (!@mkdir($dir, 0777, true)) {
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
        $dirname = dirname($filename);
        if (!file_exists($dirname)) {
            self::mkdir($dirname, $indent);
        }
        self::println($message . $filename, $indent);
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
        
        // checks if current directory is writeable
        if (!is_writeable(Atomik::get('atomik/dirs/app'))) {
            return self::fail(sprintf('%s is not writeable', Atomik::get('atomik/dirs/app')));
        }
        
        // creates the actions directory
        foreach (array_filter(Atomik::path((array) Atomik::get('atomik/dirs/actions'))) as $path) {
            self::mkdir($path, 1);
        }
            
        // creates the templates directory
        foreach (array_filter(Atomik::path((array) Atomik::get('atomik/dirs/views'))) as $path) {
            self::mkdir($path, 1);
        }
        
        // creates the plugins directory
        foreach (array_filter(Atomik::path((array) Atomik::get('atomik/dirs/plugins'))) as $path) {
            self::mkdir($path, 1);
        }
        
        // creates the includes directory
        foreach (array_filter(Atomik::path((array) Atomik::get('atomik/dirs/includes'))) as $path) {
            self::mkdir($path, 1);
        }
        
        // creates assets directory
        self::mkdir(Atomik::get('atomik/dirs/public') . '/css', 1);
        self::mkdir(Atomik::get('atomik/dirs/public') . '/js', 1);
        self::mkdir(Atomik::get('atomik/dirs/public') . '/images', 1);
        
        Atomik::fireEvent('Console::Init', array($arguments));
        
        // creates the .htaccess file
        if (in_array('--htaccess', $arguments)) {
            $trigger = Atomik::get('atomik/trigger');
            $htaccess = "<IfModule mod_rewrite.c>\n\t"
                      . "RewriteEngine on\n\t"
                      . "RewriteRule ^app/plugins/(.+)/assets - [L]\n\t"
                      . "RewriteRule ^app/ - [L,F]\n\t"
                      . "RewriteCond %{REQUEST_FILENAME} !-f\n\t"
                      .    "RewriteCond %{REQUEST_FILENAME} !-d\n\t"
                      . "RewriteRule ^(.*)$ index.php?$trigger=\$1 [L,QSA]\n"
                      . "</IfModule>";
                        
            self::touch(Atomik::get('atomik/dirs/public') . '/.htaccess', $htaccess);
        }
        
        // generate the default action scripts
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
            self::println("Generating $action");
            $filename = ltrim($action, '/') . '.php';
            
            self::touch(array_shift((array) Atomik::get('atomik/dirs/actions')) . $filename, 
                "<?php\n\n\t/* Logic goes here */\n", 1);
        
            self::touch(array_shift((array) Atomik::get('atomik/dirs/views')) . $filename, '', 1);
        
            Atomik::fireEvent('Console::Generate', array($action));
        }
    }
}
