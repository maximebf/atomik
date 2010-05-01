<?php

class FormSelectModelsHelper extends Atomik_Helper
{
    public function formSelectModels($name, $models, $keyField, $valueField, $value = '', $defaultOption = false, $attrs = array())
    {
        $options = array();
        foreach ($models as $model) {
            $options[$model->{'get' . ucfirst($keyField)}()] = $model->{'get' . ucfirst($valueField)}();
        }
        
        if (is_array($defaultOption)) {
            $options = array_merge($defaultOption, $options);
        }
        
        if (!empty($value) && $value instanceof Atomik_Model) {
            $value = $value->{'get' . ucfirst($keyField)}();
        }
        
        return $this->helpers->formSelect($name, $options, $value, $attrs);
    }
}
