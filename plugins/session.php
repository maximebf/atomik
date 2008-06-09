<?php
	/**
	 * SESSION
	 *
	 * Automatically starts a session
	 * Adds support for flash messages which are messages that
	 * are available once in the new request
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
	 * @param bool $current OPTIONAL (default false) Adds the message to the current request
	 */
	function add_flash_message($message, $current = false)
	{
		$_SESSION['__FLASH' . ($current ? '_CURRENT' : '')][] = $message;
	}
	
	/**
	 * Gets all flash messages defined for this request
	 *
	 * @return array
	 */
	function get_flash_messages()
	{
		$messages = $_SESSION['__FLASH_CURRENT'];
		$_SESSION['__FLASH_CURRENT'] = array();
		return $messages;
	}
	
	/**
	 * Retreives the next message in the stack
	 *
	 * @return string|bool Returns false when there's no more messages
	 */
	function get_flash_message()
	{
		if (count($_SESSION['__FLASH_CURRENT'])) {
			return array_shift($_SESSION['__FLASH_CURRENT']);
		}
		return false;
	}
	
