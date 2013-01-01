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

class HtmlAttributesHelper
{
    public function htmlAttributes($array, $filter = null, $exclude = true)
    {
        $attrs = array();
        foreach ($array as $key => $value) {
            if (!empty($filter) && ((!$exclude && !in_array($key, (array) $filter)) || 
                ($exclude && in_array($key, (array) $filter)))) {
                continue;
            }
            $attrs[] = sprintf('%s="%s"', $key, $value);
        }
        return implode(' ', $attrs);
    }
}
