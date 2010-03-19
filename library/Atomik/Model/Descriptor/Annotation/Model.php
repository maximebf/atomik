<?php

/** Atomik_Model_Descriptor_Annotation */
require_once 'Atomik/Model/Descriptor/Annotation.php';

/**
 * @Target("class")
 */
class Atomik_Model_Descriptor_Annotation_Model extends Atomik_Model_Descriptor_Annotation
{
    public $session;
    
    public $table;
    
    public $inheritance;
    
    public function apply(Atomik_Model_Descriptor $descriptor, $target)
    {
        if (!empty($this->session)) {
            $descriptor->setSession(Atomik_Model_Session::getInstance($this->session));
        }
        
        if (!empty($this->table)) {
            $descriptor->setTableName($this->table);
        }
        
        if (!empty($this->inheritance) && ($parentClass = $target->getParentClass()) != null) {
            $descriptor->setParentModel($parentClass->getName());
            $descriptor->setInheritanceType($this->inheritance);
        }
    }
}