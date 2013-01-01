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

class LinkToHelper
{
    public function linkTo($text, $url, $params = array(), $attrs = array()) 
    {
        $attrs['href'] = Atomik::url($url, $params);
        return sprintf('<a %s>%s</a>', 
                Atomik::htmlAttributes($attrs), $text);
    }
}
