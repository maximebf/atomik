<?php
	/*
		Atomik
		A one script PHP Framework
		
		Copyright (c) 2007, Maxime Bouroumeau-Fuseau
		Licensed under the MIT License
		Redistributions of files must retain the above copyright notice.
		(http://www.opensource.org/licenses/mit-license.php)
		
		A very (very) simple php "micro framework" allowing you
		to rigorously organize your application. Logic is 
		separated from presentation as any well coded application
		should do. 
		
		INSTALLATION
		
		Just copy this script in the root folder of your website. It should
		be name index.php. Open a command line and call:
		php index.php init [--with-htaccess] [--with-cache] [--full]
		This will create your application structure. The option "--with-htaccess"
		will also create the .htaccess file for url rewriting. "--with-cache" will create
		the cache folder. The option "--full" apply all options.
		
		HOW IT WORKS
		
		Call this script (should be index.php in your website root) with
		the page argument (GET). This one should specify the page name.
		For example: index.php?page=home will display the page called home.
		
		CONFIGURATION
		
		You can modify the configuration directly into this script. Just edit the
		lines bellow this comment under the configuration section.
		You can access configuration key using config_get(KEY). Define a value using
		config_set(KEY, VALUE).
		You can use an external config file for a cleaner style. Simply create
		config.php inside the same directory as this script and use config_set().
		
		HOW TO CREATE A PAGE
		
		A page is divided into two parts: logic and presentation. First, create
		a file "home.php" (or anything you want) inside both logic and presentation
		directories. That's all ! You can now edit the logic page to define a variable
		for example: $text = 'Hello World !';
		And now in your presentation add: <h1><?php echo $text; ?></h1>
		Go to index.php?page=home, "Hello world" is displayed !
		
		You can generate page using the command:
		php index.php generate page_name
		(Replace page_name with the name of your page)
		
		You can also only create a file in the presentation folder as pages don't
		always need logic. It act like said before but with no logic.
		
		USING A LAYOUT
		
		In common case, all pages have the same layout. To define a layout, 
		create the "_layout.php" file in the presentation directory. You can add
		the rendered content inside the layout using $content_for_layout.
		For example:
		<html>
			<head><title>Hello world</title></head>
			<body>
				<?php echo $content_for_layout; ?>
			</body>
		</html>
		If this method do not satisfy you, you can, for example, create a 
		"_header.php" and a "_footer.php" and then include them in each of your
		page's presentation (only layouts are hanlde by this script).
		
		ADD PRE AND POST DISPATCH LOGIC
		
		Sometimes you need some action to be done before and after each page. This
		can be simply done. Create a "pre_dispatch.php" and a "post_dispatch.php" file
		in the same directory as this script. Their name speak for themself. They will
		be automatically loaded.
		
		ADD A PRETTY 404 ERROR PAGE
		
		The default 404 error (when page is not found) isn't pretty at all. You can 
		create your own page by creating a "404.php" file in the same directory as
		this script.
		
		USING URL REWRITING (APACHE)
		
		A ".htaccess" file can be used to make pretty url's. For example, rather than
		"/index.php?page=home" you can have "/home" !
		Just create ".htaccess" in your website root and add the following lines:
		(mod_rewrite needs to be enable)
		
		<IfModule mod_rewrite.c>
			RewriteEngine on
			
			RewriteCond %{REQUEST_FILENAME} !-f
			RewriteCond %{REQUEST_FILENAME} !-d
			RewriteRule ^(.*)$ index.php?page=$1 [QSA,L]
			
		</IfModule>
		
		PACKAGES
		
		Helper functions are organized in packages. A package can be loaded using the
		package function. Packages are defined in the "packages" folder. The test package will
		be for example "packages/test.php". Package must be defined following this scheme:
		
		<?php
			function package_NAME()
			{
				function myFunction()
				{
				}
			}
		?>
		
		Replace NAME with your package name. It can contain any number of function.
		
		BUILT-IN PACKAGES
		
			ERROR:
				Handle errors
			
			SESSION:
				Automatically start sessions
			
			UTILS:
				Some useful functions
			
			DB:
				You can use the "db_query" function to query the database. The "db_select"
				function allow you to get rows in an array. 
				You can also get the raw results using "db_query".
				You can also use "db_insert", which take as argument the table name and
				an array listing data(array keys are column name, values are data).
				Using this function also allow you a basic database abstraction ! You can
				change your database provider with no worries about compatibility: just
				edit the configuration.
		
		Enjoy !
	
	*/
	define('ATOMIK_VERSION', '1.4');
	
	
	/***********************************************************************************************
	   										CONFIGURATION
	***********************************************************************************************/
	
	
	$_CONFIG = array(
	
		//---------------------------------------------------------------------------
		//						  Global configuration
		//---------------------------------------------------------------------------
		
		'session' 				=> true,
		'print_execution_time' 	=> true,
		
		'packages'				=> array('error', 'session', 'utils'),
		
		//---------------------------------------------------------------------------
		//						  Packages configuration
		//---------------------------------------------------------------------------
		
		'cache' 					=> false,
		'cache_pages' 				=> array(),
		'cache_time' 				=> 3600,
		
		'error_handler' 			=> true,
		'error_display' 			=> true,	
		
		'database'					=> false,
		'database_args' 			=> array('localhost', 'root', ''),
		'database_db' 				=> 'database',
		'database_func_connect' 	=> 'mysql_connect',
		'database_func_close' 		=> 'mysql_close',
		'database_func_selectdb' 	=> 'mysql_select_db',
		'database_func_query' 		=> 'mysql_query',
		'database_func_fetch' 		=> 'mysql_fetch_array',
		'database_func_escape' 		=> 'mysql_real_escape_string',
		
		//---------------------------------------------------------------------------
		//							Core configuration
		//---------------------------------------------------------------------------
		
		'page_trigger' 		=> 'page',
		'default_page' 		=> 'index',
		
		'folders_packages'		=> dirname(__FILE__) . '/packages/',
		'folders_logic' 		=> dirname(__FILE__) . '/logic/',
		'folders_presentation' 	=> dirname(__FILE__) . '/presentation/',
		'folders_includes' 		=> dirname(__FILE__) . '/includes/',
		'folders_packages' 		=> dirname(__FILE__) . '/packages/',
		'folders_cache' 		=> dirname(__FILE__) . '/cache/',
		
		'filenames_pre_dispatch' 	=> dirname(__FILE__) . '/pre_dispatch.php',
		'filenames_post_dispatch' 	=> dirname(__FILE__) . '/post_dispatch.php',
		'filenames_404' 			=> dirname(__FILE__) . '/404.php',
		'filenames_layout' 			=> dirname(__FILE__) . '/presentation/_layout.php',
		'filenames_config' 			=> dirname(__FILE__) . '/config.php'
	);
	
	
	
	
	/***********************************************************************************************
	   											CORE
	***********************************************************************************************/
	
	
	//---------------------------------------------------------------------------
	// Core
	//---------------------------------------------------------------------------
	
	config_set('start_time', time() + microtime());
	
	// loading external configuration
	if(file_exists(config_get('filenames_config')))
	{
		include(config_get('filenames_config'));
	}
	
	if(isset($_SERVER['HTTP_HOST'])) // calling from a web browser
	{		
		// default page 
		if(!isset($_GET[config_get('page_trigger')]))
		{
			config_set('current_page', config_get('default_page'));
		}
		else
		{
			config_set('current_page', $_GET[config_get('page_trigger')]);
			// checking if no dot are in the page name
			// to avoid any hack attempt
			if(strpos(config_get('current_page'), '..') !== false)
			{
				trigger404();
			}
		}
		
		// current page
		config_set('current_page_logic', config_get('folders_logic') . config_get('current_page') . '.php');
		config_set('current_page_presentation',  config_get('folders_presentation') . config_get('current_page') . '.php');
		
		// cache
		if(config_get('cache'))
		{
			config_set('current_page_cache', config_get('folders_cache') . md5($_SERVER['REQUEST_URI']) . '.php');
			if(file_exists(config_get('current_page_cache')))
			{
				ob_start();
				include(config_get('current_page_cache'));
				$cache = ob_get_clean();
				if(preg_match('/^<!--CacheTime:(\\d+)-->/', $cache, $match))
				{
					if(time() >= $match[1])
					{
						// cache has expired
						@unlink(config_get('current_page_cache'));
						unset($cache);
					}
					else
					{
						echo $cache;
						
						if(config_get('print_execution_time'))
						{
							$EXEC_TIME = round(time() + microtime() - $START_TIME, 4);
							echo "\n<!--ExecutionTime(seconds):$EXEC_TIME-->";
						}
						
						exit;
					}
				}
			}
		}
	
		// loading packages
		foreach(config_get('packages') as $package)
			package($package);
		
		// pre dispatch action
		if(file_exists(config_get('filenames_pre_dispatch')))
			include(config_get('filenames_pre_dispatch'));
		
		// loading page
		if(file_exists(config_get('current_page_logic')))
		{
			include(config_get('current_page_logic'));
		}
		elseif(file_exists(config_get('current_page_presentation')))
		{
			// no logic
		}
		else
		{
			trigger404();
		}
		
		// post dispatch (before render) actions
		if(file_exists(config_get('filenames_post_dispatch')))
			include(config_get('filenames_post_dispatch'));
		
		// rendering page
		if(file_exists(config_get('current_page_presentation')))
		{
			ob_start();
			include(config_get('current_page_presentation'));
			$content = ob_get_clean();
			
			if(file_exists(config_get('filenames_layout')))
			{
				// using layout
				$content_for_layout = $content;
				ob_start();
				include(config_get('filenames_layout'));
				$content = ob_get_clean();
				unset($content_for_layout);
			}
			
			// cache
			if(config_get('cache') && (in_array(config_get('current_page'), array_keys(config_get('cache_pages')))) ||
										in_array(config_get('current_page'), config_get('cache_pages')))
			{
				if(in_array(config_get('current_page'), array_keys(config_get('cache_pages'))))
				{
				
					$pages = config_get('cache_pages');
					$time = $pages[config_get('current_page')];
				}
				else
					$time = config_get('cache_time');
					
				// saving page
				$cache_time = time() + $time;
				$content = '<!--CacheTime:' . $cache_time . '-->' . $content;
				if($file = fopen(config_get('current_page_cache'), 'w'))
				{
					fwrite($file, $content);
					fclose($file);
				}
			}
			
			echo $content;
			
			if(config_get('print_execution_time'))
			{
				$exec_time = round(time() + microtime() - config_get('start_time'), 4);
				echo "\n<!--ExecutionTime(seconds):$exec_time-->";
			}
		}
	}
	
	
	/***********************************************************************************************
												CONSOLE
	***********************************************************************************************/
	
	
	else // calling from the command line
	{
		echo "Atomik " . ATOMIK_VERSION . " Console\n";
		if($_SERVER['argv'][1] == 'init')
		{
			echo "Init\n";
			if(is_writable(dirname(__FILE__)))
			{
				echo "\t-> Creating logic folder\n";
				mkdir(config_get('folders_logic'));
					
				echo " \t-> Creating presentation folder\n";
				mkdir(config_get('folders_presentation'));
				
				echo " \t-> Creating packages folder\n";
				mkdir(config_get('folders_packages'));
				
				echo " \t-> Creating includes folder\n";
				mkdir(config_get('folders_includes'));
				
				echo " \t-> Creating styles folder\n";
				mkdir(dirname(__FILE__) . '/styles');
				
				echo " \t-> Creating images folder\n";
				mkdir(dirname(__FILE__) . '/images');
				
				echo " \t-> Creating layout file\n";
				$layout = <<<END
<html>
	<head>
		<title>Atomik</title>
	</head>
	<body>
		<?php echo \$content_for_layout; ?>
	</body>
</html>
END;
				if($file = fopen(config_get('filenames_layout'), 'w'))
				{
					fwrite($file, $layout);
					fclose($file);
				}
				else
					echo "\t\tCan't write to file\n";
					
				if(in_array('--with-cache', $_SERVER['argv']) || in_array('--full', $_SERVER['argv']))
				{
					echo " \t-> Creating cache folder and setting permissions to 777\n";
					mkdir(config_get('folders_cache'));
					chmod(config_get('folders_cache'), 0777);
				}
				
				if(in_array('--with-htaccess', $_SERVER['argv']) || in_array('--full', $_SERVER['argv']))
				{
					echo "\t-> Creating .htaccess\n";
					$trigger = config_get('page_trigger');
					$htaccess = <<<END
<IfModule mod_rewrite.c>
	RewriteEngine on
	
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^(.*)\$ index.php?$trigger=\$1 [QSA,L]
	
</IfModule>
END;
					if($file = fopen(dirname(__FILE__) . '/.htaccess', 'w'))
					{
						fwrite($file, $htaccess);
						fclose($file);
					}
					else
						echo "\t\tCan't write to file\n";
				}
				
				$_SERVER['argv'] = array('index.php', 'generate', $_default_page);
			}
			else
			{
				echo "Current directory is not writable\n";
				exit;
			}
		}
		
		if($_SERVER['argv'][1] == 'generate')
		{
			if(count($_SERVER['argv']) == 3)
			{
				$page = $_SERVER['argv'][2];
				echo "Generating $page:\n";
				$page .= '.php';
				
				// logic
				echo "\t->Generating logic script\n";
				if($file = fopen(config_get('folders_logic') . $page, 'w'))
				{
					fwrite($file, "<?php\n\n\t// Logic goes here\n\n?>");
					fclose($file);
				}
				else
					echo "\t\tCan't write to file\n";
				
				// presentation
				echo "\t->Generating presentation script\n";
				touch(config_get('folders_presentation') . $page);
			}
			else
				echo "Missing page name";
		}
		echo "Done\n";
		exit;
	}
	
	
	/***********************************************************************************************
												FUNCTIONS
	***********************************************************************************************/
		
	
	/*
	 * Include a file from the includes folder
	 * Do not specify the extension
	 *
	 * @param string $include
	 */
	function needed($include)
	{
		require_once(config_get('folders_includes') . $include . '.php');
	}
	
	/**
	 * Load a package
	 *
	 * @param string $package
	 * @param array $arg OPTIONAL
	 */
	function package($package, $args = array())
	{
		if(!function_exists('package_' . $package))
		{
			if(file_exists(config_get('folders_packages') . $package . '.php'))
			{
				require_once(config_get('folders_packages') . $package . '.php');
			}
			else
			{
				trigger_error('Missing package: ' . $package, E_WARNING);
				return;
			}
		}
		
		call_user_func_array('package_' . $package, $args);
	}
	
	/**
	 * Get a config key
	 *
	 * @param string $key
	 * @param mixed $default OPTIONAL
	 * @return mixed
	 */
	function config_get($key, $default = '')
	{
		global $_CONFIG;
		return isset($_CONFIG[$key]) ? $_CONFIG[$key] : $default;
	}

	/**
	 * Set a config key
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	function config_set($key, $value)
	{
		global $_CONFIG;
		$_CONFIG[$key] = $value;
	}
	
	/**
	 * Check if a config key is defined
	 *
	 * @param string $key
	 * @return bool
	 */
	function config_isset($key)
	{
		global $_CONFIG;
		return isset($_CONFIG[$key]);
	}
	
	/**
	 * Trigger a 404 error
	 */
	function trigger404()
	{
		// 404 error
		header('HTTP/1.0 404 Not Found');
		if(file_exists(config_get('filenames_404')))
			include(config_get('filenames_404'));
		else
			echo '<h1>404 - File not found</h1>';
		exit;
	}
	
	
	/***********************************************************************************************
											BUILT-IN PACKAGES
	***********************************************************************************************/

	
	/**
	 * PACKAGE: ERROR
	 */
	function package_error()
	{
		/**
		 * Hanlde errors
		 *
		 * @param int $errno
		 * @param string $errstr
		 * @param string $errfile
		 * @param int $errline
		 * @param mixed $errcontext
		 */
		function error_handler($errno, $errstr, $errfile = '', $errline = 0, $errcontext = null)
		{
			if($errno <= error_reporting())
			{
				echo '<h1>Error !</h1>';
				if(config_get('error_display')) echo '<p>' . $errstr . '</p><p>Code:' . $errno . '<br/>File: ' . $errfile . '<br/>Line: ' . $errline . '</p>';
				exit;
			}
		}
		
		if(config_get('error_handler'))
			set_error_handler('error_handler');
	
	}
	
	/**
	 * PACKAGE: SESSION
	 */
	function package_session()
	{
		session_start();
	}
	
	/**
	 * PACKAGE: UTILS
	 */
	function package_utils()
	{
		/**
		 * Redirect
		 */
		function redirect($location)
		{
			@session_write_close();
			header('Location: ' . $location);
			exit;
		}
	}
	
	/**
	 * PACKAGE: DB
	 */
	function package_db()
	{
		/**
		 * Connect to the database
		 *
		 * @param array $args OPTIONAL
		 */
		function db_connect($arg = array())
		{
			if(empty($args)) $args = config_get('database_args');
			
			$_GLOBALS['db'] = call_user_func_array(config_get('database_func_connect'), $args);
			if(config_get('database_db', '') != '')
				return call_user_func(config_get('database_func_selectdb'), config_get('database_db'), $_GLOBALES['db']);
			return false;
		}
		
		/**
		 * Close the database connection
		 */
		function db_close()
		{
			return call_user_func(config_get('database_func_close'), $_GLOBALS['db']);
		}
		
		/**
		 * Query the database
		 * 
		 * @param string $query
		 * @return mixed
		 */
		function db_query($query)
		{
			return call_user_func(config_get('database_func_query'), $query);
		}
	
		/**
		 * Select data from the database
		 *
		 * @param string $query
		 * @param bool $unique OPTIONAL Only one row (default false)
		 * @return mixed
		 */
		function db_select($query, $unique = false)
		{
			if($results = db_query($query))
			{
				return db_fetch_results($results, $unique);
			}
			else
				return false;
		}
	
		/**
		 * Insert data into the database
		 * The data array keys are columns name and their
		 * associated values the data. 
		 *
		 * @param string $table
		 * @param array $data
		 * @param bool $escape OPTIONAL
		 * @return mixed
		 */
		function db_insert($table, $data, $escape = false)
		{
			$cols = array();
			$values = array();
			foreach($data as $col => $value)
			{
				$cols[] = $col;
				$values[] = ($escape ? "'" . call_user_func(config_get('database_func_escape'), $value) . "'" : $value);
			}

			$query = "INSERT INTO $table(" . implode(', ', $cols) . ") VALUES(" . implode(', ', $values) . ")";
			return db_query($query);
		}

		/**
		 * Transform raw results to an array of row
		 *
		 * @param mixed $results
		 * @param bool $unique OPTIONAL Only one row (default false)
		 * @return array
		 */
		function db_fetch_results($results, $unique = false)
		{
			global $_database_func_fetch;
		
			$rows = array();
			while($row = $_database_func_fetch($results))
			{
				$rows[] = $row;
				if($unique) break;
			}
		
			if($unique) $rows = $rows[0];
			return $rows;
		}
		
		if(config_get('database'))
			db_connect();
	}
	
		
	
	
