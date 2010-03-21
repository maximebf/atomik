<?php

/** Atomik_Model_Descriptor_Annotation */
require_once 'Atomik/Model/Descriptor/Annotation.php';

/**
 * @Target("property")
 */
class Atomik_Model_Descriptor_Annotation_Id extends Atomik_Model_Descriptor_Annotation
{
    public function apply(Atomik_Model_Descriptor $descriptor, $target)
    {
        $name = $target->getName();
        
        if (!$target->hasAnnotation('Field')) {
            throw new Atomik_Model_Descriptor_Exception(
            	"'$name' must be a field to be a primary key in '" . $descriptor->getName() . "'");
        }
        
        $field = $descriptor->getField($name);
        $descriptor->setPrimaryKeyField($field);
    }
}