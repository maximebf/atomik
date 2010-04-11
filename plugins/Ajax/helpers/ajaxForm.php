<?php

Atomik::loadHelper('form');

class AjaxFormHelper extends Atomik_Helper
{
    public function ajaxForm($action = '', $name = null, $enctype = FormHelper::ENCTYPE_URLENCODED, $method = 'POST', $attrs = array())
    {
        $attrs['class'] = Atomik::get('class', FormHelper::$defaultCSSClass, $attrs) . ' ajaxify';
        return $this->helpers($action, $name, $enctype, $method, $attrs);
    }
}
