<?php

// temporary, need complete rewrite

class Atomik_Model_Export
{
	public function export(Atomik_Model_Descriptor $descriptor)
	{
		$definition = new Atomik_Db_Schema($descriptor->getDb());
		$definition->dropBeforeCreate();
		
		$tableName = $descriptor->getTableName();
		$table = $definition->table($tableName);
		
		foreach ($descriptor->getFields() as $field) {
		    if ($field->isInherited()) {
		        continue;
		    }
		    
			$column = $table->createColumn($field->getName(), $field->getType());
			
			if ($descriptor->getIdentifierField() == $field) {
				$table->primaryKey($field->getName());
				if ($field->getType()->getName() == 'int') {
				    $column->options['auto-increment'] = true;
				}
			}
		}
		
		foreach ($descriptor->getAssociations() as $assoc) {
		    if ($assoc instanceof Atomik_Model_Association_ManyToMany) {
		        $definition->table($assoc->getViaTable())
		            ->column($assoc->getViaSourceField(), Atomik_Db_Type::factory('int'))
		            ->column($assoc->getViaTargetField(), Atomik_Db_Type::factory('int'))
		            ->index($assoc->getViaSourceField())
		            ->index($assoc->getViaTargetField());
		    }
		}
		
		$descriptor->notify('BeforeExport', $definition);
		$sql = $definition->toSql();
		$descriptor->notify('AfterExport', $sql);
		
		return $sql;
	}
}