<?php

class LinkToAjaxHelper extends Atomik_Helper
{
    public function linkToAjax($text, $url, $attrs = array()) 
    {
        $attrs['class'] = Atomik::get('class', '', $attrs) . ' ajaxify';
        return $this->helpers->linkTo($text, $url, $attrs);
    }
}