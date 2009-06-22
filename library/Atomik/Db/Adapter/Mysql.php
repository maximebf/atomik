<?php

/** Atomik_Db_Adapter_Abstract */
require_once 'Atomik/Db/Adapter/Abstract.php';

class Atomik_Db_Adapter_Mysql extends Atomik_Db_Adapter_Abstract
{
	public function quoteIdentifier($identifier)
	{
		if (strpos($identifier, '.') === false) {
			return '`' . $identifier . '`';
		}
		
		$parts = array();
		foreach (explode('.', $identifier) as $part) {
			$parts[] = '`' . $part . '`';
		}
		return implode('.', $parts);
	}
}