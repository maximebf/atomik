<?php

class SessionPlugin
{
    /** @var array */
    public static $config = array();
    
    public static function start(&$config)
    {
        $config = array_merge(array(
        
            /* @var bool */
            'autostart' => true,

            /* @var string */
            'namespace' => false
        	
        ), $config);
        self::$config = &$config;
    }
    
    public static function onAtomikStart()
    {
        if (self::$config['start_session']) {
            session_start();
            if (($ns = self::$config['session_namespace']) !== false) {
                if (!isset($_SESSION[$ns])) {
                    $_SESSION[$ns] = array();
                }
                Atomik::$store['session'] = &$_SESSION[$ns];
            } else {
                Atomik::$store['session'] = &$_SESSION;
            }
        }
    }
}
