<?php
	/**
	 * CONSOLE
	 *
	 * CLI commands to simplify administration
	 * 
	 * index.php command [arg1 [arg2 [...]]]
	 *
	 * Two builtin commands: init and generate (see end of this file)
	 *
	 * @version 1.0
	 * @package Atomik
	 * @subpackage Console
	 * @author Maxime Bouroumeau-Fuseau
	 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
	 * @license http://www.opensource.org/licenses/mit-license.php
	 * @link http://pimpmycode.fr/atomik
	 */
	 
	/* checks if we are in the CLI */
	if (isset($_SERVER['HTTP_HOST'])) {
		return;
	}

	/**
	 * Display the console and execute callbacks associated
	 * to the command
	 */
	function console_core_start()
	{
		echo "Atomik " . ATOMIK_VERSION . " Console\n";
		
		if ($_SERVER['argc'] <= 1) {
			events_fire('console_end');
			core_end();
		}
		
		/* array where registered commands are stored */
		global $_CONSOLE;
		if ($_CONSOLE === null) {
			$_CONSOLE = array();
		}
		
		/* get parameters from the command line arguments */
		$command = $_SERVER['argv'][1];
		$arguments = array_slice($_SERVER['argv'], 2);
		
		/* console starts */
		events_fire('console_start', array($command, $arguments));
		
		/* checks if the command is registered */
		if (!array_key_exists($command, $_CONSOLE)) {
			echo "The command $command does not exists\n";
			core_end();
		}
		
		/* executes the callback */
		call_user_func($_CONSOLE[$command], $arguments);
		
		/* console ends */
		events_fire('console_end', array($command, $arguments));
		
		echo "\n\nDone\n";
		core_end();
	}
	events_register('core_start', 'console_core_start');
	
	/**
	 * Registers a callback to call when a command is
	 * executed
	 *
	 * @param string $command
	 * @param callback $callback
	 */
	function console_register($command, $callback)
	{
		global $_CONSOLE;
		if ($_CONSOLE === null) {
			$_CONSOLE = array();
		}
		$_CONSOLE[$command] = $callback;
	}
	
	/**
	 * Prints a message
	 *
	 * @param string $message
	 * @param int $indent OPTIONAL Indentation
	 */
	function console_print($message, $indent = 0)
	{		
		echo "\n" . str_repeat("\t", $indent) . $message;
	}
	
	/**
	 * Prints a success message
	 *
	 * @param string $message OPTIONAL
	 * @return bool TRUE
	 */
	function console_success($message = '')
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
	function console_fail($message = '')
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
	function console_mkdir($dir, $indent = 0, $message = 'Creating directory: ')
	{
		console_print($message . $dir, $indent);
		
		/* checks if the file exists */
		if (file_exists($dir)) {
			if (!is_dir($dir)) {
				/* it exists but it's not a directory */
				return console_fail('Already exists and is not a directory');
			} else {
				/* it exists and it's a directory, no need to continue */
				return console_success('Already exists');
			}
		}
		
		/* creates the directory */
		if (!@mkdir($dir)) {
			return console_fail();
		}
		return console_success();
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
	function console_touch($filename, $content = '', $indent = 0, $message = 'Creating file: ')
	{
		console_print($message . $filename, $indent);
		
		/* writes the file */
		if (($file = fopen($filename, 'w')) === false) {
			return console_fail();
		}
		fwrite($file, $content);
		fclose($file);
		
		return console_success();
	}
	
	/**
	 * Init command
	 * index.php init [--htaccess]
	 *
	 * @param array $arguments
	 */
	function console_init($arguments)
	{
		console_print('Creating directory structure');
		
		/* checks if current directory is writeable */
		if (!is_writeable(dirname(__FILE__))) {
			return console_fail('Current directory is not writeable');
		}
		
		/* creates the actions directory */
		console_mkdir(config_get('core_paths_actions'), 1);
			
		/* creates the templates directory */
		console_mkdir(config_get('core_paths_templates'), 1);
		
		/* creates the plugins directory */
		console_mkdir(config_get('core_paths_plugins'), 1);
		
		/* creates the includes directory */
		console_mkdir(config_get('core_paths_includes'), 1);
		
		/* creates the styles directory */
		console_mkdir(dirname(__FILE__) . '/styles', 1);
		
		/* creates the images directory */
		console_mkdir(dirname(__FILE__) . '/images', 1);
		
		/* fires an event so other package can do stuff too */
		events_fire('console_init', array($arguments));
		
		/* creates the .htaccess file */
		if (in_array('--htaccess', $arguments)) {
		
			$trigger = config_get('core_url_trigger');
			$htaccess = "<IfModule mod_rewrite.c>\n\t"
			          . "RewriteEngine on\n\t"
			          . "RewriteCond %{REQUEST_FILENAME} !-f\n\t"
					  .	"RewriteCond %{REQUEST_FILENAME} !-d\n\t"
					  . "RewriteRule ^(.*)\$ index.php?$trigger=\$1 [QSA,L]\n"
					  . "</IfModule>";
						
			console_touch('.htaccess', $htaccess);
		}
		
		/* generate the default action scripts */
		console_generate(array(config_get('core_default_action')));
	}
	console_register('init', 'console_init');
	
	/**
	 * Generate command.
	 * index.php generate action_name [action_name [action_name [...]]]
	 *
	 * @param array $arguments
	 */
	function console_generate($arguments)
	{
		foreach ($arguments as $action) {
			console_print('Generating ' . $action);
			$filename = ltrim($action, '/') . '.php';
			
			/* creates the action file */
			console_touch(config_get('core_paths_actions') . $filename, 
				"<?php\n\n\t/* Logic goes here */\n", 1);
		
			/* creates the template file */
			console_touch(config_get('core_paths_templates') . $filename, '', 1);
		
			/* fires an event to allow packages to extend the generate command */
			events_fire('console_generate', array($action));
		}
	}
	console_register('generate', 'console_generate');
	
