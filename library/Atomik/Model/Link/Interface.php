<?php

interface Atomik_Model_Linkable
{
	function setBuilder(Atomik_Model_Builder $builder);
	
	function findMany($values);
	
	function findOne($values);
}