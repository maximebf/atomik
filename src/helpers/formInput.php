<?php

class FormInputHelper extends Atomik_Helper
{
    public static $defaultCSSClass = 'input';
    
    public function formInput($name, $value = '', $type = 'text', $attrs = array())
    {
        $attrs = array_merge($attrs, array(
            'name' => $name,
            'type' => $type,
            'value' => Atomik::get($name, $value, $_POST),
            'class' => Atomik::get('class', self::$defaultCSSClass, $attrs)
        ));
        
        return sprintf('<input %s />', $this->helpers->htmlAttributes($attrs));
    }
}
