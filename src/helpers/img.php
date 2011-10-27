<?php

class ImgHelper
{
    public function img($src, $alt = '', $attrs = array())
    {
        $attrs['src'] = $src;
        $attrs['alt'] = $alt;
        return sprintf('<img %s />', Atomik::htmlAttributes($attrs));
    }
}
