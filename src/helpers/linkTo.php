<?php

class LinkToHelper
{
    public function linkTo($text, $url, $params = array(), $attrs = array()) 
    {
        $attrs['href'] = Atomik::url($url, $params);
        return sprintf('<a %s>%s</a>', 
                Atomik::htmlAttributes($attrs), $text);
    }
}
