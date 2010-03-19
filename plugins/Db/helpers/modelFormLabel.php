<?php

class ModelFormLabelHelper extends Atomik_Helper
{
    public function modelFormLabel(Atomik_Model_Field $field)
    {
        $text = empty($field->form->label) ? $field->getName() : $field->form->label;
        return $this->helpers->formLabel($text);
    }
}