<?php

class EscapeHelper
{
    /**
     * Escapes text so it can be outputted safely
     * 
     * Uses escape profiles defined in the escaping configuration key
     * 
     * @param string $text The text to escape
     * @param mixed $functions A profile name, a function name, or an array of function
     * @return string The escaped string
     */
    public function escape($text, $profile = array('htmlspecialchars', 'nl2br'))
    {
        if (!is_array($profile)) {
            if (($functions = Atomik::get('helpers/escape/' . $profile, false)) === false) {
                if (function_exists($profile)) {
                    $functions = array($profile);
                } else {
                    throw new Atomik_Exception("No profile or functions named '$profile' in escape()");
                }
            }
        } else {
            $functions = $profile;
        }
        
        foreach ((array) $functions as $function) {
            $text = call_user_func($function, $text);
        }
        return $text;
    }
}

