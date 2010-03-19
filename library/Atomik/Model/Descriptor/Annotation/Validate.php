<?php

/** Atomik_Model_Descriptor_Annotation */
require_once 'Atomik/Model/Descriptor/Annotation.php';

/**
 * @Target("property")
 */
class Atomik_Model_Descriptor_Annotation_Validate extends Atomik_Model_Descriptor_Annotation
{
    public $filter;
    
    public $regexp;
    
    public $callback;
    
    public $options;
    
    public function apply(Atomik_Model_Descriptor $descriptor, $target)
    {
        $field = $descriptor->getField($target->getName());
        
        if (!empty($this->filter)) {
            require_once 'Atomik/Model/Validator/Filter.php';
            $validator = new Atomik_Model_Validator_Filter($this->filter, $this->options);
        } else if (!empty($this->regexp)) {
            require_once 'Atomik/Model/Validator/Regexp.php';
            $validator = new Atomik_Model_Validator_Regexp($this->regexp);
        } else if (!empty($this->callback)) {
            require_once 'Atomik/Model/Validator/Callback.php';
            $validator = new Atomik_Model_Validator_Callback($this->callback);
        }
        
        $field->addValidator($validator);
    }
}