<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Atomik::loadPlugin('Db');

/** Atomik_Session_Db */
require_once 'Atomik/Session/Db.php';

/**
 * @package Atomik
 * @subpackage Plugins
 */
class DbSessionPlugin
{
	/** @var array */
    public static $config = array();
    
    /**
     * @param array $config
     */
    public static function start(&$config)
    {
        $config = array_merge(array(
            'instance' => 'default',
            'table' => 'sessions',
            'idColumn' => 'session_id',
            'dataColumn' => 'session_data',
            'expiresColumn' => 'session_expires'
        ), $config);
        self::$config = &$config;
        
        $db = Atomik_Db::getInstance(self::$config['instance']);
        $table = self::$config['table'];
        $idColumn =  self::$config['idColumn'];
        $dataColumn =  self::$config['dataColumn'];
        $expiresColumn =  self::$config['expiresColumn'];
        
        $handler = new Atomik_Session_Db($db, $table, $idColumn, $dataColumn, $expiresColumn);
        Atomik_Session_Db::register($handler);
    }
	
    public static function onDbCreatesqlAfter(&$sql)
    {
		$sql .= file_get_contents(dirname(__FILE__) . '/sessions.sql');
    }
}
