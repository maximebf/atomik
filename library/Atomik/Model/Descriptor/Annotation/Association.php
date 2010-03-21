<?php

/** Atomik_Model_Descriptor_Annotation */
require_once 'Atomik/Model/Descriptor/Annotation.php';

/**
 * @Target("property")
 */
class Atomik_Model_Descriptor_Annotation_Association extends Atomik_Model_Descriptor_Annotation
{
    public $has_one;
    
    public $has_parent;
    
    public $has_many;
    
    public $has_many_to_many;
    
    public $via;
    
    public $sourceField;
    
    public $targetField;
    
    public $viaSourceColumn;
    
    public $viaTargetColumn;
    
    public $scope;
    
    public $order_by;
    
    public $limit;
    
    public function apply(Atomik_Model_Descriptor $descriptor, $target)
    {
        $name = $target->getName();
    
        if (!empty($this->has_parent)) {
            $target = Atomik_Model_Descriptor_Builder::getBase($this->has_parent);
            require_once 'Atomik/Model/Association/ManyToOne.php';
            $assoc = new Atomik_Model_Association_ManyToOne($descriptor, $name, $target);
            
        } else if (!empty($this->has_one)) {
            $target = Atomik_Model_Descriptor_Builder::getBase($this->has_one);
            require_once 'Atomik/Model/Association/OneToMany.php';
            $assoc = new Atomik_Model_Association_OneToMany($descriptor, $name, $target);
            
        } else if (!empty($this->has_many) && empty($this->via)) {
            $target = Atomik_Model_Descriptor_Builder::getBase($this->has_many);
            require_once 'Atomik/Model/Association/OneToMany.php';
            $assoc = new Atomik_Model_Association_OneToMany($descriptor, $name, $target);
            
        } else if (!empty($this->has_many_to_many) || (!empty($this->has_many) && !empty($this->via))) {
            $targetName = empty($this->has_many) ? $this->has_many_to_many : $this->has_many;
            $target = Atomik_Model_Descriptor_Builder::getBase($targetName);
            
            require_once 'Atomik/Model/Association/ManyToMany.php';
            $assoc = new Atomik_Model_Association_ManyToMany($descriptor, $name, $target);
            
            if (empty($this->via)) {
                $this->via = strtolower($descriptor->getName() . '_' . $target->getName());
            }
            
		    $assoc->setViaTable($this->via);
		    !empty($this->viaSourceColumn) && $assoc->setViaSourceColumn($this->viaSourceColumn);
		    !empty($this->viaTargetColumn) && $assoc->setViaTargetColumn($this->viaTargetColumn);
		    
        } else {
            throw new Atomik_Model_Descriptor_Exception("No target specified for association '$name' in '" 
                . $descriptor->getName() . "'");
        }
        
		!empty($this->sourceField) && $assoc->setSourceFieldName($this->sourceField);
		!empty($this->targetField) && $assoc->setTargetFieldName($this->targetField);
		
		// scope
		if (!empty($this->scope)) {
			$assoc->getQuery()->where($this->scope);
		}
		
		// order by
		if (!empty($this->order_by)) {
			$assoc->getQuery()->orderBy($this->order_by);
		}
		
		// limit
		if (!empty($this->limit)) {
			$assoc->getQuery()->limit($this->limit);
		}
    
	    if (!$descriptor->hasField($assoc->getSourceFieldName())) {
	        $descriptor->addField(
	            Atomik_Model_Field::factory($assoc->getSourceFieldName(), 'int'));
	    }
		
		$descriptor->addAssociation($assoc);
    }
}