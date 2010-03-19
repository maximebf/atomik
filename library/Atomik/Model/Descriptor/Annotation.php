<?php

require_once 'Atomik/Annotation.php';

abstract class Atomik_Model_Descriptor_Annotation extends Atomik_Annotation
{
    abstract public function apply(Atomik_Model_Descriptor $descriptor, $target);
}