<?php

class FormCheckboxHelper extends Atomik_Helper
{
    public function formCheckbox($name, $checked = false, $value = 1, $attrs = array())
    {
        if ($checked) {
            $attrs['checked'] = 'checked';
        }
        return $this->helpers->formInput($name, $value, 'checkbox', $attrs);
    }
}
