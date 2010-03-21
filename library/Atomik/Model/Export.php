<?php

class Atomik_Model_Export
{
	public function export(Atomik_Model_Descriptor $descriptor)
	{
		$definition = new Atomik_Db_Definition($descriptor->getSession()->getDbInstance());
		$definition->dropBeforeCreate();
		
		$tableName = $descriptor->getTableName();
		$table = $definition->table($tableName);
		
		foreach ($descriptor->getFields() as $field) {
			$column = $table->createColumn($field->getColumnName(), $field->getType());
			
			if ($descriptor->getPrimaryKeyField() == $field) {
				$table->primaryKey($field->getColumnName());
				if ($field->getType()->getName() == 'int') {
				    $column->options['auto-increment'] = true;
				}
			}
			
			if ($descriptor->isFieldPartOfAssociation($field) && 
			    $descriptor->getPrimaryKeyField() != $field) {
				    $table->index($field->getColumnName());
			}
		}
		
		foreach ($descriptor->getAssociations() as $assoc) {
		    if ($assoc instanceof Atomik_Model_Association_ManyToMany) {
		        $definition->table($assoc->getViaTable())
		            ->column($assoc->getViaSourceColumn(), Atomik_Db_Type::factory('int'))
		            ->column($assoc->getViaTargetColumn(), Atomik_Db_Type::factory('int'))
		            ->index($assoc->getViaSourceColumn())
		            ->index($assoc->getViaTargetColumn());
		    }
		}
		
		$descriptor->getSession()->notify('BeforeExport', $descriptor, $definition);
		$sql = $definition->toSql();
		$descriptor->getSession()->notify('AfterExport', $descriptor, $sql);
		
		return $sql;
	}
}