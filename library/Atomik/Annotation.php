<?php

require_once dirname(__FILE__) . '/Annotation/addendum/annotations.php';

Addendum::ignore('author', 'package', 'subpackage', 'category', 'param', 'return', 'see');

abstract class Atomik_Annotation extends Annotation
{
    public function getName()
    {
        $className = get_class($this);
        return substr($className, strrpos($className, '_') + 1);
    }
    
    public function toArray()
    {
        $class = new ReflectionClass($this);
        $array = array();
        
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $array[$prop->getName()] = $this->{$prop->getName()};
        }
        
        return $array;
    }
}