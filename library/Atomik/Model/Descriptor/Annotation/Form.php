<?php

/** Atomik_Model_Descriptor_Annotation */
require_once 'Atomik/Model/Descriptor/Annotation.php';

/**
 * @Target("property")
 */
class Atomik_Model_Descriptor_Annotation_Form extends Atomik_Model_Descriptor_Annotation
{
    public $helper;
    
    public $label;
    
    public function apply(Atomik_Model_Descriptor $descriptor, $target)
    {
        $descriptor->getField($target->getName())->form = $this;
    }
}