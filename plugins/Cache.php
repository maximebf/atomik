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
 * Cache plugin
 *
 * Cache the whole output of a request.
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
 * @package Atomik
 * @subpackage Plugins
 */
class CachePlugin
{
	/**
	 * Default configuration
	 * 
	 * @var array 
	 */
    public static $config = array(
    	
    	/* directory where to stored cached file */
    	'dir'			=> './app/cache/',
    	
    	/* requests to cache */
    	'requests' 		=> array(),
    	
    	/* default time for how long the cached file are used */
    	'default_time' 	=> 3600
    
    );
    
    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start($config)
    {
        /* config */
        self::$config = array_merge(self::$config, $config);
    }
    
    /**
     * Check if this request is in cache and if not starts output buffering
     */
    public static function onAtomikDispatchBefore()
    {
    	/* filename of the cached file associated to this uri */
    	$cacheFilename = Atomik::path(self::$config['dir']) . md5($_SERVER['REQUEST_URI']) . '.php';
    	self::$config['filename'] = $cacheFilename;
    	
    	/* rebuilds the cache_requests array */
    	$defaultTime = self::$config['default_time'];
    	$requests = array();
    	foreach (self::$config['requests'] as $request => $time) {
    		if ($time == 0) {
    			$requests[$request] = $defaultTime;
    		} else if ($time > 0) {
    			$requests[$request] = $time;
    		}
    	}
    	self::$config['requests'] = $requests;
    	
    	if (file_exists($cacheFilename)) {
    		$request = Atomik::get('request');
    		
    		/* last modified time */
    		$cacheTime = filemtime($cacheFilename);
    		$actionTime = filemtime(Atomik::path($request . '.php', Atomik::get('atomik/dirs/actions')));
    		$templateTime = filemtime(Atomik::path($request . '.php', Atomik::get('atomik/dirs/views')));
    		
    		/* checks if the action or the template have been modified */
    		if ($cacheTime < $actionTime || $cacheTime < $templateTime) {
    			/* invalidates the cache */
    			@unlink($cacheFilename);
    			ob_start();
    			return;
    		}
    		
    		/* checks if there is a cache limit */
    		$diff = time() - $cacheTime;
    		if ($diff > $requests[$request]) {
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
    
    /**
     * Stops output buffering and stores output in cache
     *
     * @param bool $succes Core end success
     */
    public static function onAtomikEnd($success)
    {
    	/* checks if we cache this request */
    	if (!$success) {
    		return;
    	}
    	
    	/* gets the output and print it */
    	$output = ob_get_clean();
    	echo $output;
    	
    	$cacheFilename = self::$config['filename'];
    	$request = Atomik::get('request');
    	
    	/* checks if the current url is cacheable */
    	$requests = self::$config['requests'];
    	if (isset($requests[$request])) {
    		/* saves output to file */
    		@file_put_contents($cacheFilename, $output);
    	}
    }
    
    /**
     * Creates the cache directory when the init command is used.
     * Needs the console plugin
     *
     * @param array $args
     */
    public static function onConsoleInit($args)
    {
        foreach (Atomik::path(self::$config['dir'], true) as $directory) {
        	/* creates cache directory */
        	ConsolePlugin::mkdir($directory, 1);
        	
        	/* sets permissions to 777 */
        	ConsolePlugin::println('Setting permissions', 2);
        	if (!@chmod($directory, 0777)) {
        		ConsolePlugin::fail();
        	}
        	ConsolePlugin::success();
        }
    }
}
    
