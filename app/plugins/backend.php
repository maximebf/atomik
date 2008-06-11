<?php
	/**
	 * BACKEND
	 *
	 * @version 2.0
	 * @package Atomik
	 * @subpackage Backend
	 * @author Maxime Bouroumeau-Fuseau
	 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
	 * @license http://www.opensource.org/licenses/mit-license.php
	 * @link http://pimpmycode.fr/atomik
	 */
	 
	/* default configuration */
	config_set_default(array(
	
		/* where the backend is located */
		'backend_dir'		=> './backend/',
		
		/* the database tables prefix */
		'backend_db_prefix'	=> 'atomik_'
	
	));
	
	/* needs the pdo plugin, ensures that it's loaded */
	load_plugin('pdo');

	/* needs the template parser from the backend */
	require_once config_get('backend_dir') . '/app/libraries/Atomik/Template/Parser.php';
	
	/**
	 * If the template is editable, replaces editable fields with
	 * content from the database
	 *
	 * @see atomik_render_template()
	 * @see Atomik_Template_Parser::render()
	 */
	function backend_after_template($template, &$output, &$vars, &$filename, &$echo, &$triggerError)
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
	events_register('core_after_template', 'backend_after_template');
