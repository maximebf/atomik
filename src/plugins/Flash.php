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

use Atomik;
use AtomikException;

class Flash implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Starts this class as a plugin
     */
    public static function start()
    {
        Atomik::registerHelper('flash', 'Atomik\Flash::flash');
        Atomik::registerHelper('flashMessages', 'Atomik\Flash::renderFlashMessages');
        Atomik::set('flash_messages', new Flash());
    }
    
    /**
     * Saves a message that can be retrieve only once
     * 
     * @param string|array $message One message as a string or many messages as an array
     * @param string $label
     */
    public static function flash($message, $label = 'default')
    {
        if (!isset($_SESSION)) {
            throw new AtomikException('The session must be started before using Atomik::flash()');
        }
        
        Atomik::fireEvent('Atomik::Flash', array(&$message, &$label));
        
        if (!Atomik::has("session.__FLASH.$label")) {
            Atomik::set("session.__FLASH.$label", array());
        }
        Atomik::add("session.__FLASH.$label", $message);
    }
    
    /**
     * Returns the flash messages saved in the session
     * 
     * @internal 
     * @param string $label Whether to only retreives messages from this label. When null or 'all', returns all messages
     * @param bool $delete Whether to delete messages once retrieved
     * @return array An array of messages if the label is specified or an array of array message
     */
    public static function getFlashMessages($label = null, $delete = true) {
        if (!Atomik::has('session.__FLASH')) {
            return array();
        }
        
        if ($label === null) {
        	if ($delete) {
            	return Atomik::delete('session.__FLASH');
        	}
        	return Atomik::get('session.__FLASH');
        }
        
        if (!Atomik::has("session.__FLASH.$label")) {
            return array();
        }
        
        if ($delete) {
        	return Atomik::delete("session.__FLASH.$label");
        }
        return Atomik::get("session.__FLASH.$label");
    }
    
    /**
     * Renders the messages as html
     *
     * @param string $id The wrapping ul's id
     * @return string
     */
    public static function renderFlashMessages($id = 'flash-messages')
    {
        $html = '';
    	foreach (self::getFlashMessages() as $label => $messages) {
    	    foreach ($messages as $message) {
    	        $html .= sprintf('<li class="%s">%s</li>', $label, $message);
    	    }
    	}
    	if (empty($html)) {
    	    return '';
    	}
    	return '<ul id="' . $id . '">' . $html . '</ul>';
    }

    public function getIterator()
    {
        return new \ArrayIterator(self::getFlashMessages());
    }

    public function count()
    {
        $i = 0;
        foreach (self::getFlashMessages(null, false) as $label => $msg) {
            $i += count($msg);
        }
        return $i;
    }

    public function offsetGet($label)
    {
        return self::getFlashMessages($label);
    }

    public function offsetSet($label, $value)
    {

    }

    public function offsetExists($label)
    {
        return Atomik::has("session.__FLASH.$label");
    }

    public function offsetUnset($label)
    {
        self::getFlashMessages($label);
    }
}

