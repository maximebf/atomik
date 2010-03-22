<?php

class FormHelper extends Atomik_Helper
{
    const ENCTYPE_FORMDATA = 'multipart/form-data';
    const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    
    public function form($action = '', $name = null, $enctype = FormHelper::ENCTYPE_URLENCODED, $method = 'POST', $attrs = array())
    {
        $attrs = array_merge($attrs, array(
            'action' => $action,
            'enctype' => $enctype,
            'method' => $method
        ));
        
        if ($name !== null) {
            $attrs['name'] = $name;
            $attrs['id'] = $name;
        }
        
        return '<form ' . $this->helpers->htmlAttributes($attrs) . '>';
    }
}
