<?php

class FormHiddenHelper extends Atomik_Helper
{
    public function formHidden($name, $value = '', $attrs = array())
    {
        return $this->helpers->formInput($name, $value, 'hidden', $attrs);
    }
}
