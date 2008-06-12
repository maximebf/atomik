<?php
/**
 * Atomik Framework
 * 
 * @package Atomik
 * @subpackage Session
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * Session plugin
 *
 * Automatically starts a session
 * Adds support for flash messages which are messages that
 * are available once in the next request
 *
 * @package Atomik
 * @subpackage Session
 */
class SessionPlugin
{
    /**
     * Messages for the current session
     *
     * @var array
     */
    protected static $_messages = array();
    
    /**
     * Plugin initialization
     *
     * @return bool
     */
    public static function start()
    {
        /* starts the session */
        @session_start();
        
        /* messages for the current request */
        if (isset($_SESSION['__FLASH'])) {
        	/* adds message defined in the last request in the current one */
        	self::$_messages = $_SESSION['__FLASH'];
        }
        
        /* messages for the next request */
        $_SESSION['__FLASH'] = array();
        
        /* cleany close the session when atomik ends */
        Atomik::registerEvent('Atomik::End', 'session_write_close');
        
        /* no needs to automatically register events */
        return false;
    }
    
	/**
	 * Adds a message that will be accessible on the next request
	 *
	 * @param string $message
	 * @param string $label OPTIONAL anything you want, error or valid for example
	 * @param bool $current OPTIONAL (default false) Adds the message to the current request
	 */
	public static function flash($message, $label = 'default', $current = false)
	{
	    /* gets the messages array in which to add the message */
	    if ($current) {
	        $messages = &self::$_messages;
	    } else {
	        $messages = &$_SESSION['__FLASH'];
	    }
	    
		/* checks if the label exists */
		if (!isset($messages[$label])) {
			$messages[$label] = array();
		}
		
		$messages[$label][] = $message;
	}
	
	/**
	 * Gets all flash messages defined for this request
	 *
	 * @param string $label OPTIONAL (default null) Set to null to retreive all messages
	 * @return array
	 */
	public static function getMessages($label = null)
	{
		/* if $label is null, returns all the messages of all labels */
		if ($label === null) {
			$messages = self::$_messages;
			self::$_messages = array();
			return $messages;
		}
		
		/* checks if the label exists */
		if (!isset(self::$_messages[$label])) {
			return array();
		}
		
		/* gets all messages and empty the array */
		$messages = self::$_messages[$label];
		self::$_messages[$label] = array();
		
		return $messages;
	}
	
	/**
	 * Retreives the next message in the stack
	 *
	 * @param string $label OPTIONAL (default null) Set to null to retreive from all messages
	 * @return array|string|bool Returns false when there's no more messages
	 */
	public static function getNextMessage($label = null)
	{
		/* if $label is null, returns the next message whatever the label */
		if ($label === null) {
			/* no labels */
			if (count(self::$_messages) == 0) {
				return false;
			}
			
			/* gets the next message */
			foreach (self::$_messages as $label => $messages) {
				$message = array_shift($messages);
				break;
			}
			
			/* deletes the label if no more messages */
			if (count(self::$_messages[$label]) == 0) {
				unset(self::$_messages[$label]);
			}
			
			return array($label, $message);
		}
		
		/* checks if the label exists */
		if (!isset(self::$_messages[$label])) {
			return false;
		}
		
		/* returns the next message */
		$message = array_shift(self::$_messages[$label]);
		
		/* deletes the label if no more messages */
		if (count(self::$_messages[$label]) == 0) {
			unset(self::$_messages[$label]);
		}
		
		return $message;
	}
	
	/**
	 * Returns the number of messages
	 *
	 * @return int
	 */
	public static function countMessages($label = null)
	{
		/* if $label is null, returns all the messages of all labels */
		if ($label === null) {
			$count = 0;
			foreach (self::$_messages as $msgs) {
				$count += count($msgs);
			}
			return $count;
		}
		
		/* checks if the label exists */
		if (!isset(self::$_messages[$label])) {
			return 0;
		}
		
		return count(self::$_messages[$label]);
	}
}
