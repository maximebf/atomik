<?php

class Atomik_Model_Export
{
	public function export(Atomik_Model_Descriptor $descriptor)
	{
		$definition = new Atomik_Db_Definition($descriptor->getManager()->getDbInstance());
		$definition->dropBeforeCreate();
		
		$tableName = $descriptor->tableName;
		$table = $definition->table($tableName);
		
		foreach ($descriptor->getFields() as $field) {
			list($type, $length) = $field->getSqlType();
			$column = $table->createColumn($field->name, $type, $length, $field->getOptions('sql-'));
			
			if ($descriptor->getPrimaryKeyField() == $field) {
				$table->primaryKey($field->name);
				$column->options['auto-increment'] = true;
			}
			
			if ($descriptor->isFieldPartOfReference($field)) {
				$table->index($field->name, $field->getOption('sql-index', null));
			}
		}
		
		$descriptor->getBehaviourBroker()->notifyBeforeExport($descriptor, $definition);
		$sql = $definition->toSql();
		$descriptor->getBehaviourBroker()->notifyAfterExport($descriptor, $sql);
		
		return $sql;
	}
}