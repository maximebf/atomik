<?php

class FormSelectHelper extends Atomik_Helper
{
    public static $defaultCSSClass = 'input';
    
    public function formSelect($name, $options, $value = '', $attrs = array())
    {
        $attrs = array_merge($attrs, array(
            'name' => $name,
            'value' => Atomik::get($name, $value, $_POST),
            'class' => Atomik::get('class', self::$defaultCSSClass, $attrs)
        ));
        
        $html = sprintf('<select %s>', $this->helpers->htmlAttributes($attrs));
        foreach ($options as $key => $value) {
            $html .= sprintf('<option value="%s">%s</option>', $key, $value);
        }
        $html .= '</select>';
        
        return $html;
    }
}
