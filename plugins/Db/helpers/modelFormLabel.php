<?php

class ModelFormLabelHelper extends Atomik_Helper
{
    public function modelFormLabel($field)
    {
        $text = $field->getOption('label', $field->name);
        $required = $field->getOption('required', false);
        return $this->helpers->formLabel($text, $required);
    }
}