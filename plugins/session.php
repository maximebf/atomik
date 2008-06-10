<?php
	/**
	 * SESSION
	 *
	 * Automatically starts a session
	 * Adds support for flash messages which are messages that
	 * are available once in the next request
	 *
	 * @version 2.0
	 * @package Atomik
	 * @subpackage Session
	 * @author Maxime Bouroumeau-Fuseau
	 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
	 * @license http://www.opensource.org/licenses/mit-license.php
	 * @link http://pimpmycode.fr/atomik
	 */
	 
	/* starts the session when the core starts */
	function session_core_start()
	{
		@session_start();
		
		/* messages for the current request */
		$_SESSION['__FLASH_CURRENT'] = array();
		if (isset($_SESSION['__FLASH'])) {
			/* adds message defined in the last request in the current one */
			$_SESSION['__FLASH_CURRENT'] = $_SESSION['__FLASH'];
		}
		
		/* messages for the next request */
		$_SESSION['__FLASH'] = array();
	}
	events_register('core_start', 'session_core_start');
	
	/* ends the session when the core ends */
	events_register('core_end', 'session_write_close');
	
	/**
	 * Adds a message that will be accessible on the next request
	 *
	 * @param string $message
	 * @param string $label OPTIONAL anything you want, error or valid for example
	 * @param bool $current OPTIONAL (default false) Adds the message to the current request
	 */
	function add_flash_message($message, $label = 'default', $current = false)
	{
		/* checks if the session key exists */
		$key = '__FLASH' . ($current ? '_CURRENT' : '');
		if (!isset($_SESSION[$key])) {
			$_SESSION[$key] = array();
		}
		
		/* checks if the label exists */
		if (!isset($_SESSION[$key][$label])) {
			$_SESSION[$key][$label] = array();
		}
		
		$_SESSION[$key][$label][] = $message;
	}
	
	/**
	 * Gets all flash messages defined for this request
	 *
	 * @param string $label OPTIONAL (default null) Set to null to retreive all messages
	 * @return array
	 */
	function get_flash_messages($label = null)
	{
		/* if $label is null, returns all the messages of all labels */
		if ($label === null) {
			$messages = $_SESSION['__FLASH_CURRENT'];
			$_SESSION['__FLASH_CURRENT'] = array();
			return $messages;
		}
		
		/* checks if the label exists */
		if (!isset($_SESSION['__FLASH_CURRENT'][$label])) {
			return array();
		}
		
		/* gets all messages and empty the array */
		$messages = $_SESSION['__FLASH_CURRENT'][$label];
		$_SESSION['__FLASH_CURRENT'][$label] = array();
		
		return $messages;
	}
	
	/**
	 * Retreives the next message in the stack
	 *
	 * @param string $label OPTIONAL (default null) Set to null to retreive from all messages
	 * @return array|string|bool Returns false when there's no more messages
	 */
	function get_flash_message($label = null)
	{
		/* if $label is null, returns the next message whatever the label */
		if ($label === null) {
			/* no labels */
			if (count($_SESSION['__FLASH_CURRENT']) == 0) {
				return false;
			}
			
			/* gets the next message */
			foreach ($_SESSION['__FLASH_CURRENT'] as $label => $messages) {
				$message = array_shift($messages);
				break;
			}
			
			/* deletes the label if no more messages */
			if (count($_SESSION['__FLASH_CURRENT'][$label]) == 0) {
				unset($_SESSION['__FLASH_CURRENT'][$label]);
			}
			
			return array($label, $message);
		}
		
		/* checks if the label exists */
		if (!isset($_SESSION['__FLASH_CURRENT'][$label])) {
			return false;
		}
		
		/* returns the next message */
		$message = array_shift($_SESSION['__FLASH_CURRENT'][$label]);
		
		/* deletes the label if no more messages */
		if (count($_SESSION['__FLASH_CURRENT'][$label]) == 0) {
			unset($_SESSION['__FLASH_CURRENT'][$label]);
		}
		
		return $message;
	}
	
