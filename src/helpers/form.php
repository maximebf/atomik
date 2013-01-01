<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atomik\Helpers;

use Atomik;

class FormHelper
{
    const ENCTYPE_FORMDATA = 'multipart/form-data';
    const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    
    public $action;
    
    public $name;
    
    public $attrs = array();
    
    public function form($action = '', $name = null, $attrs = array())
    {
        $this->action = $action;
        $this->name = $name;
        $this->attrs = $attrs;
        return $this;
    }
    
    public function open($action = '', $name = null, $attrs = array())
    {
        $attrs = array_merge($attrs, array(
            'action' => $action,
            'method' => Atomik::get('method', 'POST', $attrs),
            'class' => Atomik::get('class', Atomik::get('helpers.form.default_form_class', ''), $attrs)
        ));
        
        if ($name !== null) {
            $attrs['name'] = $name;
            $attrs['id'] = $name;
        }
        
        return '<form ' . Atomik::htmlAttributes($attrs) . '>';
    }
    
    public function __toString()
    {
        return $this->open($this->action, $this->name, $this->attrs);
    }
    
    public function label($label, $required = false, $attrs = array())
    {
        return sprintf('<label %s>%s %s</label>', 
            Atomik::htmlAttributes($attrs),
            $label,
            $required ? '<span class="required">*</span>' : ''
        );
    }
    
    public function input($name, $value = '', $type = 'text', $attrs = array())
    {
        $attrs = array_merge($attrs, array(
            'name' => $name,
            'type' => $type,
            'value' => Atomik::get($name, $value, $_POST),
            'class' => Atomik::get('class', Atomik::get('helpers.form.default_input_class', ''), $attrs)
        ));
        
        return sprintf('<input %s />', Atomik::htmlAttributes($attrs));
    }
    
    public function checkbox($name, $checked = false, $value = 1, $attrs = array())
    {
        if ($checked) {
            $attrs['checked'] = 'checked';
        }
        return $this->input($name, $value, 'checkbox', $attrs);
    }
    
    public function file($name, $attrs = array())
    {
        return $this->input($name, '', 'file', $attrs);
    }
    
    public function hidden($name, $value = '', $attrs = array())
    {
        return $this->input($name, $value, 'hidden', $attrs);
    }
    
    public function select($name, $options, $value = '', $attrs = array())
    {
        $attrs = array_merge($attrs, array(
            'name' => $name,
            'class' => Atomik::get('class', Atomik::get('helpers.form.default_select_class', ''), $attrs)
        ));
        
        $html = sprintf('<select %s>', Atomik::htmlAttributes($attrs));
        foreach ($options as $key => $text) {
            $html .= sprintf('<option value="%s"%s>%s</option>', 
                $key, 
                $key == $value ? ' selected="selected"' : '',
                Atomik::escape($text)
            );
        }
        $html .= '</select>';
        
        return $html;
    }
    
    public function textarea($name, $value = '', $attrs = array())
    {
        $attrs = array_merge($attrs, array(
            'name' => $name,
            'class' => Atomik::get('class', Atomik::get('helpers.form.default_textarea_class', ''), $attrs)
        ));
        
        return sprintf('<textarea %s>%s</textarea>', 
                    Atomik::htmlAttributes($attrs), 
                    Atomik::get($name, $value, $_POST));
    }
    
    public function button($text, $type = 'submit', $attrs = array())
    {
        $attrs = array_merge($attrs, array(
            'type' => $type,
            'class' => Atomik::get('class', Atomik::get('helpers.form.default_button_class', ''), $attrs)
        ));
        
        return sprintf('<button %s>%s</button>', Atomik::htmlAttributes($attrs), $text);
    }
    
    public function buttons($submitText = 'Submit', $cancelUrl = 'javascript:history.back()', $buttonAttrs = array(), $cancelText = 'or <a href="%s">cancel</a>')
    {
        $html = $this->button($submitText, 'submit', $buttonAttrs);
        
        if ($cancelUrl !== false) {
            $html .= sprintf($cancelText, $cancelUrl);
        }
        
        return $html;
    }
    
}
