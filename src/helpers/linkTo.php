<?php

class LinkToHelper extends Atomik_Helper
{
    public function linkTo($text, $url, $attrs = array()) 
    {
        $attrs['href'] = $url;
        return sprintf('<a %s>%s</a>', 
                $this->helpers->htmlAttributes($attrs), $text);
    }
}