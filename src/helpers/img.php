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

class ImgHelper
{
    public function img($src, $alt = '', $attrs = array())
    {
        $attrs['src'] = Atomik::asset($src);
        $attrs['alt'] = $alt;
        return sprintf('<img %s />', Atomik::htmlAttributes($attrs));
    }
}
