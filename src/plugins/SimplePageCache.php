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
    AtomikException;

/**
 * Cache the whole output of a request.
 *
 * Specify which requests to cache using the requests
 * config key. requests value must be an array where its keys
 * are the request and its values the time in second the cached version will
 * be used.
 * Example:
 *
 * array(
 *    'index' => 60,
 *    'view'  => 3600
 * )
 *
 * The index action will be catched for 60 seconds and the view action
 * for one hour.
 * Use 0 to use the default cache time defined in default_time
 *
 * @package Atomik
 * @subpackage Plugins
 */
class SimplePageCache
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
        
            // directory where to stored cached file
            'dir'            => 'cache',
            
            // requests to cache
            'requests'       => array(),
            
            // default time for how long the cached file are used
            'default_time'   => 3600
        
        ), $config);
         self::$config = &$config;
    }
    
    /**
     * Check if this request is in cache and if not starts output buffering
     */
    public static function onAtomikDispatchBefore()
    {
        // filename of the cached file associated to this uri
        $cacheFilename = Atomik::path(self::$config['dir']) . md5($_SERVER['REQUEST_URI']) . '.php';
        self::$config['filename'] = $cacheFilename;
        
        // rebuilds the cache_requests array
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
            $cacheTime = filemtime($cacheFilename);
            
            // checks if there is a cache limit
            $diff = time() - $cacheTime;
            if ($diff > $requests[$request['action']]) {
                @unlink($cacheFilename);
                ob_start();
                return;
            }
            
            // cache still valid, output the cache content
            readfile($cacheFilename);
            exit;
        }
        
        ob_start();
    }
    
    /**
     * Stops output buffering and stores output in cache
     *
     * @param bool $succes Core end success
     */
    public static function onAtomikEnd($success)
    {
        // checks if we cache this request
        if (!$success) {
            return;
        }
        
        $output = ob_get_clean();
        echo $output;
        
        $cacheFilename = self::$config['filename'];
        $request = Atomik::get('request');
        
        // checks if the current url is cacheable
        $requests = self::$config['requests'];
        if (isset($requests[$request['action']])) {
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
        foreach (array_filter(Atomik::path((array) self::$config['dir'])) as $dir) {
            Console::mkdir($dir, 1);
        }
    }
}
    
