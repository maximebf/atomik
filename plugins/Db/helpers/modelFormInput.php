<?php

class ModelFormInputHelper extends Atomik_Helper
{
    public static $defaultHelper = 'formInput';
    
    public static $helperMap = array(
        'text' => 'formTextarea',
        'bool' => 'formYesNo',
        'datetime' => 'formDatetime'
    );
    
    public function modelFormInput(Atomik_Model_Field $field, Atomik_Model $model)
    {
        $name = $field->getName();
        $value = $model->_get($name);
        $helper = Atomik::get($field->getType()->getName(), self::$defaultHelper, self::$helperMap);
        $args = array($name, $value);
        
        return Atomik::helper($helper, $args);
    }
}