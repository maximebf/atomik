<?php

class Atomik_Model_Export_Sql
{
	public function export(Atomik_Model_Builder $builder)
	{
		$typeMap = $builder->getManager()->getTypeMap();
		$definition = new Atomik_Db_Definition($builder->getManager()->getDbInstance());
		
		$tableName = $builder->tableName;
		$table = $definition->table($tableName);
		
		foreach ($builder->getFields() as $field) {
			list($type, $length) = $typeMap->map($field->type, $field->getOption('length', null));
			$table->column($field->name, $type, $length, $field->getOptions('sql-'));
			
			if ($builder->isFieldPartOfReference($field)) {
				$table->index($field->name, $field->getOption('sql-index', null));
			}
		}
		
		return $definition->toSql();
	}
}