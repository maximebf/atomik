<?php

interface Atomik_Db_Adapter_Interface
{
	function __construct(PDO $pdo);
	function getQueryGenerator();
	function getSchemaGenerator();
	function quote($value);
	function quoteIdentifier($identifier);
}