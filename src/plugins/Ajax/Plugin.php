<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Ajax plugin
 *
 * @package Atomik
 * @subpackage Plugins
 */
class AjaxPlugin
{
	/** @var array */
    public static $config = array();
    
    /** @var bool */
    protected static $enabled = false;
    
    /**
     * Returns false if it's not an AJAX request so
     * events are not automatically registered
     * 
     * @param array $config
     * @return bool
     */
    public static function start(&$config)
    {
        $config = array_merge(array(
        	// disabe layouts for all templates when it's an ajax request
        	'disable_layout'	=> true
        ), $config);
        
    	self::$config = &$config;
		Atomik::add('atomik/dirs/helpers', dirname(__FILE__) . '/helpers');
        
        /* checks if this is an ajax request */
        self::$enabled = isset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }
    
	/**
	 * Checks if it's needed to disable the layout
	 */
	public static function onAtomikStart()
	{
		if (self::$enabled && self::$config['disable_layout']) {
			Atomik::disableLayout();
		}
	}

	/**
	 * After an allowed action has been executed, transform its $vars to
	 * a json string and echo it. Exits right after.
	 *
	 * @see Atomik::execute()
	 */
	public static function onAtomikExecuteAfter($action, &$context, &$vars)
	{
	    if (!self::$enabled) {
	        return;
	    }
	    
        header('Content-type: text/html; charset=utf-8');
        self::addFlashMessagesHeader();
	
        // in case setEnabled() as been called after Atomik::Start
        if (self::$config['disable_layout']) {
            Atomik::disableLayout();
        }
		
		self::endWithJson($vars);
	}
    
    /**
     * Prevents from redirections
     */
    public static function onAtomikRedirect()
    {
        if (self::$enabled) {
            self::addFlashMessagesHeader();
            Atomik::end(true);
        }
    }
    
    /**
     * Sends flash messages as an http header so they can be used in javascript
     */
    public static function addFlashMessagesHeader()
    {
        header('Flash-messages: ' . json_encode(Atomik::get('flash:all')));
    }
	
	/**
	 * Output vars as json and exits
	 *
	 * @param array $vars
	 */
	public static function endWithJson($vars = array())
	{
		/* builds the json string */
		$json = json_encode($vars);
		
		/* echo's output */
		header('Content-type: application/json; charset=utf-8');
		echo $json;
		
		/* ends successfuly */
		Atomik::end(true);
	}
	
	/**
	 * Checks if the current request has been made with AJAX
	 * 
	 * @return bool
	 */
	public static function isEnabled()
	{
		return self::$enabled;
	}
	
	/**
	 * @param bool $enabled
	 */
	public static function setEnabled($enabled = true)
	{
	    self::$enabled = $enabled;
	}
}

