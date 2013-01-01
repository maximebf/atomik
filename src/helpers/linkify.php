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

class LinkifyHelper
{
    public function linkify($string)
    {
        $string = str_replace('-', ' ', $string);
        $string = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $string);
        $string = trim(strtolower($string));
        return $string;
    }
}
