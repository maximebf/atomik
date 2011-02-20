<?php

class FormRichTextareaHelper extends Atomik_Helper
{
    public function formRichTextarea($name, $value = '', $uEditor = array(), $attrs = array())
    {
        Atomik_Assets::getInstance()->addNamedAsset('uEditor');
        
        $uEditor = array_merge(array(
            'toolbarItems' => array('bold', 'italic', 'link', 'image', 'orderedlist', 'unorderedlist')
        ), $uEditor);
        
        $id = uniqid();
        $attrs['id'] = $id;
        
        $html = $this->helpers->formTextarea($name, $value, $attrs);
        $html .= '<script type="text/javascript">$(function() { '
               . sprintf('$(\'#%s\').uEditor(%s);', $id, json_encode($uEditor))
               . ' });</script>';
        
        return $html;
    }
}
