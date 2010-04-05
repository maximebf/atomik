<?php

interface Atomik_Model_Query_Filter_Interface
{
	function apply(Atomik_Db_Query $query);
}