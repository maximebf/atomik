<?php
/**
 * Atomik Framework
 *
 * @package Atomik
 * @subpackage Backend
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */
	
/* needs the db plugin, ensures that it's loaded */
Atomik::loadPlugin('db');

/**
 * Frontend plugin
 * 
 * Enables backend features in the user application
 *
 * @package Atomik
 * @subpackage Backend
 */
class BackendPlugin
{
	/**
	 * Default configuration
	 * 
	 * This is the minimum needed configuration for the frontend plugins to
     * work. It is however greatly advice to define all the backend options
     * in your config file
	 * 
	 * @var array 
	 */
    public static $config = array(

    	/* where the backend is located */
    	'dir'		=> './backend/',
    	
    	/* the database tables prefix */
    	'db_prefix'	=> 'atomik_'
    	
	);
	
    /**
     * Whether the plugin is used in the backend or
     * in the frontend 
     *
     * @var bool
     */
    protected static $_backend;
    
    /**
     * Requires Atomik_Template_Parser
     * 
     * @param array $config
     */
    public static function start($config)
    {
        /* config */
        self::$config = array_merge(self::$config, $config);
        self::$_backend = defined('ATOMIK_BACKEND');
        
        /* checks if it's used in the frontend */
        if (!self::$_backend) {
            /** Atomik_Backend_Page */
            require_once self::$config['dir'] . 'app/libraries/Atomik/Backend/Page.php';
            /* registers the event */
            Atomik::listenEvent('Atomik::Render::After', 
                array('BackendPlugin', 'frontendOnAtomikRenderAfter'));
                
        	/* do not auto register events */
            return false;
        }
        
        /* ensures that this plugin is loaded after the controller plugin */
        Atomik::loadPlugin('controller');
    }
    
	
	/* -------------------------------------------------------------------------------------------
	 *  Frontend
	 * ------------------------------------------------------------------------------------------ */
	
	
    /**
     * If the template is editable, replaces editable fields with
     * content from the database
     *
     * @see atomik_render_template()
     * @see Atomik_Template_Parser::render()
     */
    public static function frontendOnAtomikRenderAfter($template, &$output, &$vars, &$filename, &$echo, &$triggerError)
    {
        /* sets the backend database prefix during this function */
        $currentPrefix = Db::$config['prefix'];
        Db::$config['prefix'] = self::$config['db_prefix'];
        
    	/* creates the Atomik_Backend_Page instance, returns false if not editable */
    	if (($page = Atomik_Backend_Page::fromFile($filename)) === false) {
    	    return;
    	}
    	
    	/* replaces editable fields */
    	$output = $page->render();
    	
        Db::$config['prefix'] = $currentPrefix;
    }
    
	
	/* -------------------------------------------------------------------------------------------
	 *  Backend
	 * ------------------------------------------------------------------------------------------ */

    
	/**
	 * Sets the template path to the one of the current module
	 */
	public static function onControllerRouterEnd($request)
	{
	    $dirs = Atomik::path(Atomik::get('atomik/dirs/templates'), true);
	    $dirs[] = './app/modules/' . $request['controller'] . '/templates/';
	    Atomik::set('atomik/dirs/templates', $dirs);
	}
    
	/**
	 * Changes the action name for atomik to find the file
	 */
	public static function onAtomikExecuteStart(&$action, &$template)
	{
	    $request = Atomik::get('controller_request');
		$action = $request['controller'] . '/controller';
		$template = $request['action'];
	}
    
	/**
	 * Changes the template path
	 */
	public static function onControllerDispatchAfter($request, $instance, &$template, &$vars)
	{
		$template = $request['action'];
	}
}
