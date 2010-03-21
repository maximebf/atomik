<?php

class FormSelectModelsHelper extends Atomik_Helper
{
    public function formSelectModels($name, $models, $keyField, $valueField, $value = '', $attrs = array())
    {
        $options = array();
        foreach ($models as $model) {
            $options[$model->_get($keyField)] = $model->_get($valueField);
        }
        
        if (!empty($value) && $value instanceof Atomik_Model) {
            $value = $value->_get($keyField);
        }
        
        return $this->helpers->formSelect($name, $options, $value, $attrs);
    }
}
