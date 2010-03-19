<?php

class ModelFormInputHelper extends Atomik_Helper
{
    public function modelFormInput(Atomik_Model_Field $field, Atomik_Model $model)
    {
        $name = $field->name;
        $value = $model->{$name};
        
        switch(gettype($field)) {
            default:
                return $this->helpers->formInput($name, $value);
        }
    }
}