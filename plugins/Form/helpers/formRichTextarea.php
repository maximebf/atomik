<?php

class FormRichTextareaHelper extends Atomik_Helper
{
    public function formRichTextarea($name, $value = '', $uEditor = array(), $attrs = array())
    {
        $uEditor = array_merge(array(
            'toolbarItems' => array('bold', 'italic', 'link', 'image', 'orderedlist', 'unorderedlist')
        ), $uEditor);
        
        $html = $this->helpers->formTextarea($name, $value, $attrs);
        $html .= '<script type="text/javascript">$(function() { '
               . sprintf('$(\'#%s\').uEditor(%s);', $name, json_encode($uEditor))
               . ' });</script>';
        
        return $html;
    }
}
