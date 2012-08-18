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

class ClassQL
{
    public static function start($config)
    {
        $config = array_merge(array(

            'model_dirs' => array('Models' => 'models')

        ), $config);

        \ClassQL\Session::start($config);

        $loader = new \ClassQL\ModelLoader();
        $loader->add(array_filter((array) Atomik::path($config['model_dirs'])));
        $loader->register();

        if (Atomik::isPluginLoaded('Console')) {
            Console::register('classql', function($argv) {
                $cli = new \ClassQL\CLI();
                $cli->run($argv);
            });
        }
    }
}
