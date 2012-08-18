<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Atomik::loadPluginIfAvailable('Console');
Atomik::loadPlugin('Db');
Atomik::loadPluginIfAvailable('Config');
Atomik::loadPlugin('Assets');
Atomik::loadPlugin('Auth');

/**
 * Backend plugin
 * 
 * @package Atomik
 * @subpackage Plugins
 */
class BackendPlugin
{
	/**
	 * Default configuration
	 * 
	 * @var array 
	 */
	public static $config = array(
		
		// the route needed to start the backend
		'route' => 'backend*',
	
		'title' => 'Atomik Backend'
	
	);
	
	/**
	 * Plugin initialization
	 *
	 * @param array $config
	 * @return bool
	 */
	public static function start($config)
	{
        self::$config = array_merge(self::$config, $config);
        Atomik::set('backend', self::$config);
        Atomik::registerPluggableApplication('Backend', self::$config['route']);
        AuthPlugin::addRestrictedUri(self::$config['route'], array('backend'));
	}
}
