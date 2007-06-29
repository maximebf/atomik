<?php
	/*
		Atomik
		A one script PHP Framework
		
		Copyright (c) 2007, Maxime Bouroumeau-Fuseau
		Licensed under the MIT License
		Redistributions of files must retain the above copyright notice.
		(http://www.opensource.org/licenses/mit-license.php)
		
		A very (very) simple php "framework" allowing you
		to rigorously organize your application. Logic is 
		separated from presentation as any well coded application
		should do. Support database connection and cache !
		
		INSTALLATION
		
		Just copy this script in the root folder of your website. It should
		be name index.php. Open a command line and call:
		php index.php init [--with-htaccess] [--with-cache]
		This will create your application structure. The option "--with-htaccess"
		will also create the .htaccess file for url rewriting. "--with-cache" will create
		the cache folder.
		
		HOW IT WORKS
		
		Call this script (should be index.php in your website root) with
		the page argument (GET). This one should specify the page name.
		For example: index.php?page=home will display the page called home.
		
		CONFIGURATION
		
		You can modify the configuration directly into this script. Just edit the
		lines bellow this comment under the configuration section.
		You can use an external config file for a cleaner style. Simply create
		config.php inside the same directory as this script.
		
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
		
		DATABASE
		
		You can define a database connection. It will automatically started and 
		stopped. Modify the lines in the database connection section.
		You can use the "db_query" function to query the database. The "db_select"
		function allow you to get rows in an array. 
		You can also get the raw results using "db_query".
		You can also use "db_insert", which take as argument the table name and
		an array listing data(array keys are column name, values are data).
		Using this function also allow you a basic database abstraction ! You can
		change your database provider with no worries about compatibility: just
		edit the configuration.
		
		ADD PRE AND POST DISPATCH LOGIC
		
		Sometimes you need some action to be done before and after each page. This
		can be simply done. Create a "pre_dispatch.php" and a "post_dispatch.php" file
		in the same directory as this script. Their name speak for themself. They will
		be automatically loaded.
		
		ADD A PRETTY 404 ERROR PAGE
		
		The default 404 error (when page is not found) isn't pretty at all. You can 
		create your own page by creating a "404.php" file in the same directory as
		this script. You can access global variables like $current_page.
		
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
		
		
		Enjoy !
	
	*/
	define('ATOMIK_VERSION', '1.2');
	
	// -------------------------------------------------------------------------------------------------------
	// Application configuration
	
	// session are automatically started
	$_auto_session = true;
	
	// print execution time at the end of the page (html comment)
	$_print_execution_time = true;
	
	//cache
	$_cache_enabled = false;
	$_cache_pages = array(); // pages which should be cached (name without extension)
	$_cache_time = 3600; // in second
	
	
	// -------------------------------------------------------------------------------------------------------
	// Database connection
	
	// whether to connect to a database or not
	$_database = false; 
	
	// connection information
	$_database_args = array('localhost', 'root', ''); // in the order of the connection function
	$_database_db = 'database'; // comment this line if no database selection is needed
	
	// functions (default are for mysql)
	$_database_func_conn = 'mysql_connect';
	$_database_func_deconn = 'mysql_close';
	$_database_func_selectdb = 'mysql_select_db';
	$_database_func_query = 'mysql_query';
	$_database_func_fetch = 'mysql_fetch_array';
	$_database_func_escape = 'mysql_escape_string';
	
	
	// -------------------------------------------------------------------------------------------------------
	// Core configuration
	
	// page
	$_page_trigger = 'page'; // the GET argument containing the page name
	$_default_page = 'index';
	
	// folders
	$_logic_folder = dirname(__FILE__) . '/logic/';
	$_presentation_folder = dirname(__FILE__) . '/presentation/';
	$_includes_folder = dirname(__FILE__) . '/includes/';
	$_cache_folder = dirname(__FILE__) . '/cache/';
	
	// filenames
	$_pre_dispatch_filename = dirname(__FILE__) . '/pre_dispatch.php';
	$_post_dispatch_filename = dirname(__FILE__) . '/post_dispatch.php';
	$_404_filename = dirname(__FILE__) . '/404.php';
	$_layout_filename = $_presentation_folder . '_layout.php';
	$_config_filename = dirname(__FILE__) . '/config.php';
	
	
	
	// -------------------------------------------------------------------------------------------------------
	//					DO NOT EDIT BELLOW
	// -------------------------------------------------------------------------------------------------------
	
	
	// -------------------------------------------------------------------------------------------------------
	// Core
	
	$START_TIME = time() + microtime();
	
	// loading external configuration
	if(file_exists($_config_filename))
	{
		include($_config_filename);
	}
	
	if(isset($_SERVER['HTTP_HOST'])) // calling from a web browser
	{
		// default page 
		if(!isset($_GET[$_page_trigger]))
			$_GET[$_page_trigger] = $_default_page;
			
		// current page
		$current_page = $_GET[$_page_trigger];
		$current_page_logic = $_logic_folder . $current_page . '.php';
		$current_page_presentation = $_presentation_folder . $current_page . '.php';
		
		// cache
		if($_cache_enabled)
		{
			$current_page_cache = $_cache_folder . md5($_SERVER['REQUEST_URI']) . '.php';
			if(file_exists($current_page_cache))
			{
				ob_start();
				include($current_page_cache);
				$cache = ob_get_clean();
				if(preg_match('/^<!--CacheTime:(\\d+)-->/', $cache, $match))
				{
					if(time() >= $match[1])
					{
						// cache has expired
						@unlink($current_page_cache);
						unset($cache);
					}
					else
					{
						echo $cache;
						
						if($_print_execution_time)
						{
							$EXEC_TIME = round(time() + microtime() - $START_TIME, 4);
							echo "\n<!--ExecutionTime(seconds):$EXEC_TIME-->";
						}
						
						exit;
					}
				}
			}
		}
		
		// session
		if($_auto_session)
			session_start();
		
		// database connection
		if($_database)
		{
			$db = call_user_func_array($_database_func_conn, $_database_args);
			if(isset($_database_db))
				call_user_func($_database_func_selectdb, $_database_db, $db);
		}
		
		// pre dispatch action
		if(file_exists($_pre_dispatch_filename))
			include($_pre_dispatch_filename);
		
		// loading page
		if(file_exists($current_page_logic))
		{
			include($current_page_logic);
		}
		elseif(file_exists($current_page_presentation))
		{
			// no logic
		}
		else
		{
			// closing database
			if($_database)
				call_user_func($_database_func_deconn, $db);
				
			// 404 error
			header("HTTP/1.0 404 Not Found");
			if(file_exists($_404_filename))
				include($_404_filename);
			else
				echo '<h1>404 - File not found</h1>';
			exit;
		}
		
		// post dispatch (before render) actions
		if(file_exists($_post_dispatch_filename))
			include($_post_dispatch_filename);
			
		// database disconnection
		if($_database)
		{
			call_user_func($_database_func_deconn, $db);
		}
		
		// rendering page
		if(file_exists($current_page_presentation))
		{
			ob_start();
			include($current_page_presentation);
			$content = ob_get_clean();
			
			if(file_exists($_layout_filename))
			{
				// using layout
				$content_for_layout = $content;
				ob_start();
				include($_layout_filename);
				$content = ob_get_clean();
				unset($content_for_layout);
			}
			
			// cache
			if($_cache_enabled && in_array($current_page, $_cache_pages))
			{
				// saving page
				$cache_time = time() + $_cache_time;
				$content = '<!--CacheTime:' . $cache_time . '-->' . $content;
				$file = fopen($current_page_cache, 'w');
				fwrite($file, $content);
				fclose($file);
			}
			
			echo $content;
			
			if($_print_execution_time)
			{
				$EXEC_TIME = round(time() + microtime() - $START_TIME, 4);
				echo "\n<!--ExecutionTime(seconds):$EXEC_TIME-->";
			}
		}
	}
	
	// -------------------------------------------------------------------------------------------------------
	// Console
	
	else // calling from the command line
	{
		echo "Atomik " . ATOMIK_VERSION . " Console\n";
		if($_SERVER['argv'][1] == 'init')
		{
			echo "Init\n";
			if(is_writable(dirname(__FILE__)))
			{
				echo "\t-> Creating logic folder\n";
				mkdir($_logic_folder);
					
				echo " \t-> Creating presentation folder\n";
				mkdir($_presentation_folder);
				
				echo " \t-> Creating includes folder\n";
				mkdir($_includes_folder);
				
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
				$file = fopen($_layout_filename, 'w');
				fwrite($file, $layout);
				fclose($file);
				
				if(in_array('--with-cache', $_SERVER['argv']) || in_array('--full', $_SERVER['argv']))
				{
					echo " \t-> Creating cache folder and setting permissions to 777\n";
					mkdir($_cache_folder);
					@chmod($_cache_folder, 0777);
				}
				
				if(in_array('--with-htaccess', $_SERVER['argv']) || in_array('--full', $_SERVER['argv']))
				{
					echo "\t-> Creating .htaccess\n";
					$htaccess = <<<END
<IfModule mod_rewrite.c>
	RewriteEngine on
	
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^(.*)\$ index.php?page=\$1 [QSA,L]
	
</IfModule>
END;
					$file = fopen(dirname(__FILE__) . '/.htaccess', 'w');
					fwrite($file, $htaccess);
					fclose($file);
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
				$file = fopen($_logic_folder . $page, 'w');
				fwrite($file, "<?php\n\n\t// Logic goes here\n\n?>");
				fclose($file);
				
				// presentation
				echo "\t->Generating presentation script\n";
				touch($_presentation_folder . $page);
			}
			else
				echo "Missing page name";
		}
		echo "Done\n";
		exit;
	}
	
	
	// -------------------------------------------------------------------------------------------------------
	// Functions
	
	
	/*
	 * Include a file from the includes folder
	 * Do not specify the extension
	 *
	 * @param string $include
	 */
	function needed($include)
	{
		global $_includes_folder;
		require_once($_includes_folder . $include . '.php');
	}

	/**
	 * Query the database
	 * 
	 * @param string $query
	 * @return mixed
	 */
	function db_query($query)
	{
		global $_database_func_query;
		return $_database_func_query($query);
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
		$results = db_query($query);
		return db_fetch_results($results, $unique);
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
		global $_database_func_escape;
		
		$cols = array();
		$values = array();
		foreach($data as $col => $value)
		{
			$cols[] = $col;
			$values[] = ($escape ? "'" . $_database_func_escape($value) . "'" : $value);
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
		}
		
		if($unique) $rows = $rows[0];
		return $rows;
	}
	
	/**
	 * Redirect
	 */
	function redirect($location)
	{
		@session_write_close();
		header('Location: ' . $location);
		exit;
	}
