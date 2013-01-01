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

use Atomik;
use ConsoleKit;
use Exception;
use AtomikException;

class Console
{
    /** @var array */
    public static $config = array();
    
    /** @var array */
    private static $commands = array();

    /** @var ConsoleKit\Console */
    public static $console;
    
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
            'scripts_dir'    => 'app/scripts'
            
        ), $config);
        self::$config = &$config;

        self::$console = new ConsoleKit\Console();
        self::$console->addCommand('Atomik\Console::generate');
        
        // checks if we are in the CLI
        if (PHP_SAPI !== 'cli') {
            return false;
        }
    }
    
    /**
     * Display the console and execute callbacks associated
     * to the command
     */
    public static function onAtomikStart()
    {
        $paths = (array) self::$config['scripts_dir'];
        foreach (Atomik::getLoadedPlugins(true) as $plugin => $path) {
            $paths[] = "$path/scripts";
        }

        foreach (array_filter(array_map('realpath', $paths)) as $path) {
            self::$console->addCommandsFromDir($path, '', true);
        }

        self::$console->run();
        exit();
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
        self::$console->addCommand($callback, $command);
    }
    
    /**
     * Generate command.
     * index.php generate action_name [action_name [action_name [...]]]
     *
     * @param array $arguments
     */
    public static function generate($args, $opts, $console)
    {
        $checklist = new ConsoleKit\Widgets\Checklist($console);
        foreach ($args as $action) {
            $console->writeln("Generating '$action'");
            $filename = ltrim($action, '/') . '.php';

            $actionsDir = (array) Atomik::get('atomik.dirs.actions');
            $actionsDir = array_shift($actionsDir);
            $checklist->step("Creating action file in $actionsDir", function() use ($actionsDir, $filename) {
                return ConsoleKit\FileSystem::touch(ConsoleKit\FileSystem::join($actionsDir, $filename), 
                    "<?php\n\n// Logic goes here\n");
            });
        
            $viewsDir = (array) Atomik::get('atomik.dirs.views');
            $viewsDir = array_shift($viewsDir);
            $checklist->step("Creating view file in $viewsDir", function() use ($viewsDir, $filename) {
                return ConsoleKit\FileSystem::touch(ConsoleKit\FileSystem::join($viewsDir, $filename));
            });
        
            Atomik::fireEvent('Console::Generate', array($action));
        }
    }
}
