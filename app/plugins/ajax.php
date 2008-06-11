<?php
	/**
	 * AJAX
	 * 
	 * @version 2.0
	 * @package Atomik
	 * @subpackage Ajax
	 * @author Maxime Bouroumeau-Fuseau
	 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
	 * @license http://www.opensource.org/licenses/mit-license.php
	 * @link http://pimpmycode.fr/atomik
	 */
	
	/* default configuration */
	config_set_default(array(
		
		/* disabe layouts for all templates when it's an ajax request */
		'disable_layout'	=> true,
	
		/* default action: restrict all (and use the ajax_allowed array) 
		 * or allow all (and use the ajax_restricted array) */
		'ajax_allow_all'	=> true,
		
		/* actions where ajax won't be available */
		'ajax_restricted'	=> array(),
		
		/* actions where ajax will be available */
		'ajax_allowed'		=> array()
	
	));
	
	/* checks if this is an ajax request */
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
		return;
	}
	
	/**
	 * Checks if it's needed to disable the layout
	 */
	function ajax_core_start()
	{
		if (config_get('disable_layout', true)) {
			/* needs the disable_layout() function (layout plugin) */
			if (function_exists('disable_layout')) {
				disable_layout();
			}
		}
	}
	events_register('core_start', 'ajax_core_start');

	/**
	 * After an allowed action has been executed, transform its $vars to
	 * a json string and echo it. Exists right after.
	 *
	 * @see atomik_execute_action()
	 */
	function ajax_after_action($action, &$template, &$vars, &$render, &$echo, &$triggerError)
	{
		if (!$echo) {
			return;
		}
		
		/* checks if ajax is enabled for this action */
		if (config_get('ajax_allow_all', true)) {
			if (in_array($action, config_get('ajax_restricted'))) {
				return;
			}
		} else {
			if (!in_array($action, config_get('ajax_allowed'))) {
				return;
			}
		}
		
		/* builds the json string */
		$json = json_encode($vars);
		
		/* echo's output */
		header('Content-type: application/json');
		echo $json;
		atomik_end();
	}
	events_register('core_after_action', 'ajax_after_action');


	/* checks if the json_encode() function is available */
	if (!function_exists('json_encode')) {
		/**
		 * Encode a php array into a json string
		 * Very simple encoder, advise to use PHP5 or an external lib
		 * 
		 * @param array $array
		 * @return string
		 */
		function json_encode($array)
		{
			if (!is_array($array)) {
				if (is_string($array)) {
					return '"' . $array . '"';
				} else if (is_bool($array)) {
					return $array ? 'true' : 'false';
				} else {
					return $array;
				}
			}
			
			/* checks if it's an associative array */
			if (!empty($array) && (array_keys($array) !== range(0, count($array) - 1))) {
				$items = array();
				foreach ($array as $key => $value) {
					$items[] = "'$key': " . json_encode($value);
				}
				return '{' . implode(', ', $items) . '}';
			}
			
			/* standard array */
			$items = array();
			foreach ($array as $value) {
				$items[] = json_encode($value);
			}
			return '[' . implode(', ', $items) . ']';
		}
	}
	
