<?php
	/**
	 * CACHE
	 *
	 * Cache the whole output or a request.
	 *
	 * To enable the cache, sets the cache config key to true.
	 *
	 * Specify which requests to cache using the cache_requests
	 * config key. cache_requests value must be an array where its keys
	 * are the request and its values the time in second the cached version will
	 * be used.
	 * Example:
	 *
	 * array(
	 *    'index' => 60,
	 *    'view'  => 3600
	 * )
	 *
	 * The /index request will be catched for 60 seconds and the /view request
	 * for one hour.
	 *
	 * Other possible time value:
	 *   0 = use default time
	 *  -1 = infinite
	 *
	 * The cache is regenerated without taking in consideration time when the
	 * action or the template associated to the request are modified.
	 *
	 * @version 2.0
	 * @package Atomik
	 * @subpackage Cache
	 * @author Maxime Bouroumeau-Fuseau
	 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
	 * @license http://www.opensource.org/licenses/mit-license.php
	 * @link http://pimpmycode.fr/atomik
	 */
	 
	/* default configuration */
	config_set_default(array(
		/* enable/disable the cache */
		'cache' 				=> false,
		/* directory where to stored cached file */
		'cache_dir'				=> config_get('core_paths_root') . 'cache/',
		/* requests to cache */
		'cache_requests' 		=> array(),
		/* default time for how long the cached file are used */
		'cache_default_time' 	=> 3600
	));

	/**
	 * Check if this request is in cache and if not starts output buffering
	 */
	function cache_check()
	{
		/* checks if the cache is enabled */
		if (config_get('cache', false) === false) {
			return;
		}
		
		/* filename of the cached file associated to this uri */
		$cacheFilename = config_get('cache_dir') . md5($_SERVER['REQUEST_URI']) . '.php';
		config_set('cache_filename', $cacheFilename);
		
		/* rebuilds the cache_requests array */
		$defaultTime = config_get('cache_default_time', 3600);
		$requests = array();
		foreach (config_get('cache_requests') as $request => $time) {
			if ($time == 0) {
				$requests[$request] = $defaultTime;
			} else if ($time > 0) {
				$requests[$request] = $time;
			}
		}
		config_set('cache_requests', $requests);
		
		if (file_exists($cacheFilename)) {
			/* last modified time */
			$cacheTime = filemtime($cacheFilename);
			$actionTime = filemtime(config_get('request_action'));
			$templateTime = filemtime(config_get('request_template'));
			
			/* checks if the action or the template have been modified */
			if ($cacheTime < $actionTime || $cacheTime < $templateTime) {
				/* invalidates the cache */
				@unlink($cacheFilename);
				ob_start();
				return;
			}
			
			/* checks if there is a cache limit */
			$diff = time() - $cacheTime;
			if ($diff > $requests[config_get('request_url')]) {
				/* invalidates the cache */
				@unlink($cacheFilename);
				ob_start();
				return;
			}
			
			/* cache still valid, output the cache content */
			readfile($cacheFilename);
			
			exit;
		}
		
		/* starts output buffering */
		ob_start();
	}
	events_register('core_before_dispatch', 'cache_check');

	/**
	 * Stops output buffering and stores output in cache
	 *
	 * @param bool $succes Core end success
	 */
	function cache_save($success)
	{
		/* checks if we cache this request */
		if (!$success || config_get('cache', false) === false) {
			return;
		}
		
		/* gets the output and print it */
		$output = ob_get_clean();
		echo $output;
		
		$cacheFilename = config_get('cache_filename');
		$url = config_get('request_url');
		
		/* checks if the current url is cacheable */
		$requests = config_get('cache_requests');
		if (isset($requests[$url])) {
			/* saves output to file */
			if ($file = fopen($cacheFilename, 'w')) {
				fwrite($file, $output);
				fclose($file);
				return;
			}
			trigger_error('Error writing to cache', E_USER_WARNING);
		}
	}
	events_register('core_end', 'cache_save');

	/**
	 * Creates the cache directory when the init command is used.
	 * Needs the console plugin
	 *
	 * @param array $args
	 */
	function cache_console_init($args)
	{
		$directory = config_get('cache_dir');
		
		if (in_array('--with-cache', $args) || in_array('--full', $args)) {
			/* creates cache directory */
			console_mkdir($directory, 1);
			
			/* sets permissions to 777 */
			console_print('Setting permissions for cache directory', 1);
			if (!@chmod($directory, 0777)) {
				console_fail();
			}
			console_success();
		}
	}
	events_register('console_init', 'cache_console_init');
	
