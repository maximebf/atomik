<?php

class ImgHelper extends Atomik_Helper
{
    public function img($src, $alt = '', $attrs = array())
    {
        $attrs['src'] = $src;
        $attrs['alt'] = $alt;
        return sprintf('<img %s />', $this->helpers->htmlAttributes($attrs));
    }
}