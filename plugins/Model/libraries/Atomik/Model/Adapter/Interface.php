<?php

/**
 * Interface for model's adapter
 *
 */
interface Atomik_Model_Adapter_Interface
{
	static function getInstance();
	
	function findAll(Atomik_Model_Builder $builder, $where = null, $orderBy = '', $limit = '');
	
	function find(Atomik_Model_Builder $builder, $where, $orderBy = '', $limit = '');
	
	function save(Atomik_Model $model);
	
	function delete(Atomik_Model $model);
}