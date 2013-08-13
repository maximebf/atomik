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

class Session
{
    /** @var array */
    public static $config = array();
    
    /**
     * Starts this class as a plugin
     *
     * @param array $config
     */
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
        if (self::$config['autostart']) {
            session_start();
            if (($ns = self::$config['namespace']) !== false) {
                if (!isset($_SESSION[$ns])) {
                    $_SESSION[$ns] = array();
                }
                Atomik::$store['session'] = &$_SESSION[$ns];
            } else {
                Atomik::$store['session'] = &$_SESSION;
            }
            Atomik::fireEvent('Session::Start', array($ns));
        }
    }
}
