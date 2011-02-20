<?php

class FormButtonHelper extends Atomik_Helper
{
    public static $defaultCSSClass = 'button';
    
    public function formButton($text, $type = 'submit', $attrs = array())
    {
        $attrs = array_merge($attrs, array(
            'value' => $text,
            'type' => $type,
            'class' => Atomik::get('class', self::$defaultCSSClass, $attrs)
        ));
        
        return sprintf('<input %s />', $this->helpers->htmlAttributes($attrs) );
    }
}
