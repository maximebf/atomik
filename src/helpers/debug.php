<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atomik\Helpers;

use Atomik;

class DebugHelper
{
    /**
     * Equivalent to var_dump() but can be disabled using the configuration
     *
     * @see var_dump()
     * @param mixed $data The data which value should be dumped
     * @param bool $force Always display the dump even if debug from the config is set to false
     * @param bool $echo Whether to echo or return the result
     * @return string The result or null if $echo is set to true
     */
    public function debug($data, $force = false, $echo = true)
    {
        if (!$force && !Atomik::get('app.debug', false)) {
            return;
        }
        
        Atomik::fireEvent('Atomik::Debug', array(&$data, &$force, &$echo));
        
        // var_dump() does not support returns
        ob_start();
        var_dump($data);
        $dump = ob_get_clean();
        
        if (!$echo) {
            return $dump;
        }
        echo $dump;
    }
}
