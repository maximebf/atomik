<?php

/** Atomik_Model_Descriptor_Annotation */
require_once 'Atomik/Model/Descriptor/Annotation.php';

/** Atomik_Db_Type */
require_once 'Atomik/Db/Type.php';

/**
 * @Target("property")
 */
class Atomik_Model_Descriptor_Annotation_Field extends Atomik_Model_Descriptor_Annotation
{
    public $type = 'string';
    
    public $length;
    
    public $columnName;
    
    public $repr = false;
    
    public $required = false;
    
    public function apply(Atomik_Model_Descriptor $descriptor, $target)
    {
        $name = $target->getName();
        $type = Atomik_Db_Type::factory($this->type);
        
        if ($this->length !== null) {
            $type->setLength($this->length);
        }
        
        $field = new Atomik_Model_Field($name, $type);
        $field->setRequired($this->required);
        
        if (!empty($this->columnName)) {
            $field->setColumnName($this->columnName);
        }
        
        $descriptor->addField($field);
        
        if ($this->repr) {
            $descriptor->setRepresentationField($field);
        }
    }
}