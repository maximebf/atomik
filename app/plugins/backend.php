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
 
/* default configuration 
 * this is the minimum needed configuration for the backend plugins to
 * work. It is however greatly advice to define all the backend options
 * in your config file */
Atomik::setDefault(array(
    'backend' => array(

    	/* where the backend is located */
    	'dir'		=> './backend/',
    	
    	/* the database tables prefix */
    	'db_prefix'	=> 'atomik_'
    	
	)
));
	
/* needs the db plugin, ensures that it's loaded */
Atomik::loadPlugin('db');

/**
 * Backend plugin
 *
 * @package Atomik
 * @subpackage Backend
 */
class BackendPlugin
{
    /**
     * Requires Atomik_Template_Parser
     */
    public static function start()
    {
        /** Atomik_Template_Parser */
        require_once Atomik::get('backend/dir') . 'app/libraries/Atomik/Template/Parser.php';
    }
    
    /**
     * If the template is editable, replaces editable fields with
     * content from the database
     *
     * @see atomik_render_template()
     * @see Atomik_Template_Parser::render()
     */
    public static function onAtomikRenderAfter($template, &$output, &$vars, &$filename, &$echo, &$triggerError)
    {
    	/* gets the content and loads the template */
    	$content = file_get_contents($filename);
    	$parser = new Atomik_Template_Parser($content, realpath($filename));
    	
    	/* checks if the file is editable */
    	if (!$parser->isEditable()) {
    		return;
    	}
    	
    	/* replaces editable fields */
    	$output = $parser->render();
    }
}
