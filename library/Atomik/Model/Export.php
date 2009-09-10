<?php

class Atomik_Model_Export
{
	public function export(Atomik_Model_Builder $builder)
	{
		$definition = new Atomik_Db_Definition($builder->getManager()->getDbInstance());
		$definition->dropBeforeCreate();
		
		$tableName = $builder->tableName;
		$table = $definition->table($tableName);
		
		foreach ($builder->getFields() as $field) {
			list($type, $length) = $field->getSqlType();
			$column = $table->createColumn($field->name, $type, $length, $field->getOptions('sql-'));
			
			if ($builder->getPrimaryKeyField() == $field) {
				$table->primaryKey($field->name);
				$column->options['auto-increment'] = true;
			}
			
			if ($builder->isFieldPartOfReference($field)) {
				$table->index($field->name, $field->getOption('sql-index', null));
			}
		}
		
		$builder->getBehaviourBroker()->notifyBeforeExport($builder, $definition);
		$sql = $definition->toSql();
		$builder->getBehaviourBroker()->notifyAfterExport($builder, $sql);
		
		return $sql;
	}
}