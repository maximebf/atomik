<?php

class FormSelectModelsHelper extends Atomik_Helper
{
    public function formSelectModels($name, $models, $keyField, $valueField, $value = '', $attrs = array())
    {
        $options = array();
        foreach ($models as $model) {
            $options[$model->getProperty($keyField)] = $model->getProperty($valueField);
        }
        
        if (!empty($value) && $value instanceof Atomik_Model) {
            $value = $value->getProperty($keyField);
        }
        
        return $this->helpers->formSelect($name, $options, $value, $attrs);
    }
}
