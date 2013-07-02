<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atomik\Controller;

use Atomik;
use AtomikException;
use AtomikHttpException;

/**
 * Controller plugin
 * 
 * @package Atomik
 * @subpackage Plugins
 */
class Plugin
{
    /** @var array */
    public static $config = array();
    
    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start(&$config)
    {
        $config = array_merge(array(
        
            // default action name
            'default_action' => 'index',
        
            // directories where to find controllers
            'dirs' => array('app/actions', 'app/controllers'),

            // default controller namespaces
            'namespace' => ''
            
        ), $config);
        
        self::$config = &$config;
        Atomik::set('app.executor', 'Atomik\Controller\Plugin::execute');
        Atomik::add('atomik.dirs.includes', array_filter(Atomik::path((array) self::$config['dirs'])));
    }
    
    /**
     * Executor which defines controllers and actions MVC-style
     *
     * @param string $action
     * @param string $method
     * @param array  $context
     * @return array
     */
    public static function execute($action, $method, $vars, &$context)
    {
        $controller = trim(dirname($action), './');
        $action = basename($action);
        if (empty($controller)) {
            $controller = $action;
            $action = self::$config['default_action'];
        }

        $className = trim(self::$config['namespace'] . '\\' 
                   . str_replace(' ', '\\', ucwords(str_replace('/', ' ', $controller))) 
                   . 'Controller', '\\');
        
        Atomik::fireEvent('Controller::Execute', array(&$className));

        if (!class_exists($className)) {
            throw new AtomikHttpException("Class '$className' not found", 404);
        } else if (!is_subclass_of($className, 'Atomik\Controller\Controller')) {
            throw new AtomikException("Class '$className' must subclass 'Atomik\Controller\Controller'");
        }
        
        $instance = new $className();
        if (($vars = $instance->_dispatch($action, $method, $vars)) === false) {
            return false;
        }
        
        if (!is_array($vars)) {
            $vars = array();
        }
        return array_merge(get_object_vars($instance), $vars);
    }
}

