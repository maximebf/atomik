<?php

class FormFileHelper extends Atomik_Helper
{
    public function formFile($name, $attrs = array())
    {
        return $this->helpers->formInput($name, '', 'file', $attrs);
    }
}
