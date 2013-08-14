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
use DebugBar\StandardDebugBar;
use Psr\Log\LogLevel;

class DebugBar
{
    public static $config = array();

    public static $instance;

    public static $renderer;

    private static $atomikMessages;

    /**
     * Starts this class as a plugin
     *
     * @param array $config
     */
    public static function start(&$config)
    {
        self::$config = &$config;
        self::$instance = new StandardDebugBar();
        self::$renderer = self::$instance->getJavascriptRenderer();
        self::$renderer->setOptions(self::$config);

        self::$atomikMessages = new \DebugBar\DataCollector\MessagesCollector('atomik');
        self::$instance['messages']->aggregate(self::$atomikMessages);

        Atomik::set('debugbar', self::$instance);
        Atomik::registerHelper('renderDebugBar', array(self::$renderer, 'render'));
        Atomik::registerHelper('renderDebugBarHead', array(self::$renderer, 'renderHead'));
    }

    public static function log($message, $level = LogLevel::DEBUG)
    {
        self::$instance['mesages']->addMessage($message, $level);
    }

    public static function onAtomikBootstrap()
    {
        self::$instance->addCollector(new \DebugBar\DataCollector\ConfigCollector(Atomik::get()));
    }

    public static function onAtomikStart(&$cancel)
    {
        if (Atomik::isPluginLoaded('Db')) {
            Atomik::set('db', new \Atomik\DebugBar\TraceableDb(Atomik::get('db')));
            self::$instance->addCollector(new \DebugBar\DataCollector\PDO\PDOCollector(Atomik::get('db'), self::$instance['time']));
        }

        if (Atomik::isPluginLoaded('Logger')) {
            Atomik::listenEvent('Logger::log', array(self::$instance['messages'], 'addMessage'));
        }
    }

    public static function onAtomikDispatchUri(&$uri, &$request, &$cancel)
    {
        if (!isset(self::$config['base_url'])) {
            self::$renderer->setBaseUrl(Atomik::get('atomik.base_url') . 'vendor/maximebf/debugbar/src/DebugBar/Resources');
        }
        self::$atomikMessages->addMessage("Dispatching '$uri' to '{$request['action']}'", LogLevel::DEBUG);
    }

    public static function onAtomikExecuteBefore(&$action, &$context, &$vars)
    {
        self::$atomikMessages->addMessage("Executing action '$action'", LogLevel::DEBUG);
        self::$instance['time']->startMeasure("execute $action", "Execute '$action'");
    }

    public static function onAtomikExecuteAfter($action, &$context, &$vars)
    {
        self::$instance['time']->stopMeasure("execute $action");
    }

    public static function onAtomikRenderBefore(&$view, &$vars, &$filename)
    {
        self::$atomikMessages->addMessage("Rendering view '$view'", LogLevel::DEBUG);
        self::$instance['time']->startMeasure("render $view", "Render '$view'");
    }

    public static function onAtomikRenderAfter($view, &$output, $vars, $filename)
    {
        try {
            self::$instance['time']->stopMeasure("render $view");
        } catch (\DebugBar\DebugBarException $e) {
            // the last layout triggers an exception because the collectors are collected 
            // while it is rendered
        }
    }

    public static function onAtomikLoadhelperAfter($helperName, $dirs)
    {
        self::$atomikMessages->addMessage("Loaded helper '$helperName'", LogLevel::DEBUG);
    }

    public static function onAtomikPluginAfter($plugin)
    {
        self::$atomikMessages->addMessage("Loaded plugin '$plugin'", LogLevel::DEBUG);
    }

    public static function onAtomikEnd($success, &$writeSession)
    {
        self::$atomikMessages->addMessage("Ending (success=$success)", LogLevel::DEBUG);
    }

    public static function onSessionStart($ns)
    {
        self::$atomikMessages->addMessage("Session started", LogLevel::DEBUG);
    }

    public static function onAtomikHttperror($e, &$cancel)
    {
        self::$instance['exceptions']->addException($e);
    }

    public static function onAtomikError($e, &$cancel)
    {
        self::$instance['exceptions']->addException($e);
    }
}
