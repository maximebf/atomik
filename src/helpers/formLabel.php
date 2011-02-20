<?php

class FormLabelHelper extends Atomik_Helper
{
    public function formLabel($label, $required = false, $attrs = array())
    {
        return sprintf('<label %s>%s %s</label>', 
            $this->helpers->htmlAttributes($attrs),
            $label,
            $required ? '<span class="required">*</span>' : ''
        );
    }
}
