<?php

class FormTextareaHelper extends Atomik_Helper
{
    public static $defaultCSSClass = 'input';
    
    public function formTextarea($name, $value = '', $attrs = array())
    {
        $attrs = array_merge($attrs, array(
            'name' => $name,
            'id' => $name,
            'class' => Atomik::get('class', self::$defaultCSSClass, $attrs)
        ));
        
        return sprintf('<textarea %s>%s</textarea>', 
                    $this->helpers->htmlAttributes($attrs), $value);
    }
}
