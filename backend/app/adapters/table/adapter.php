<?php

require_once 'Atomik/Backend/Model/Adapter/Interface.php';

class TableAdapter implements Atomik_Backend_Model_Adapter_Interface 
{
	public static function getValues($id, $modelId)
	{
		$row = Db::find('models_tables', array('model_id' => $modelId));
		return Db::find($row['table'], array($row['primary_key'] => $id));
	}
}