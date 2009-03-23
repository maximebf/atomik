<?php

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Atomik/Db/Query.php';

class Atomik_Db_QueryTest extends PHPUnit_Framework_TestCase
{
	public function testSelect()
	{
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT *', (string) $query->select());
		
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT f1, f2', (string) $query->select('f1', 'f2'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT f1, f2', (string) $query->select(array('f1', 'f2')));
	}
	
	public function testFrom()
	{
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT * FROM table', (string) $query->select()->from('table'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT f1 FROM table', (string) $query->select('f1')->from('table'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT f1 FROM table1, table2', 
			(string) $query->select('f1')->from('table1')->from('table2'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT * FROM table AS t', (string) $query->select()->from('table', 't'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT * FROM table AS t', (string) $query->select()->from(array('table' => 't')));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT * FROM table1 AS t1, table2 AS t2', 
			(string) $query->select()->from(array('table1' => 't1', 'table2' => 't2')));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT * FROM table AS t1, table AS t2', 
			(string) $query->select()->from('table', 't1')->from('table', 't2'));
	}
	
	public function testJoin()
	{
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT * FROM table INNER JOIN table2 ON a = b', 
			(string) $query->select()->from('table')->join('table2', 'a = b'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT * FROM table INNER JOIN table2 AS t2 ON a = b', 
			(string) $query->select()->from('table')->join('table2', 'a = b', 't2'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT * FROM table LEFT JOIN table2 ON a = b', 
			(string) $query->select()->from('table')->join('table2', 'a = b', null, 'LEFT'));
			
		$query = new Atomik_Db_Query();
		$this->assertEquals('SELECT * FROM table INNER JOIN table2 ON a = b LEFT JOIN table3 AS t3 ON a = c', 
			(string) $query->select()->from('table')->join('table2', 'a = b')->join('table3', 'a = c', 't3', 'LEFT'));
	}
	
	public function testWhere()
	{
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT * FROM table WHERE a = 'b'", (string) $query->select()->from('table')->where("a = 'b'"));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT * FROM table WHERE a = ?", (string) $query->select()->from('table')->where(array('a' => 'b')));
		$this->assertContains('b', $query->getParams());
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT * FROM table WHERE a = ?", (string) $query->select()->from('table')->orWhere('a = ?', 'b'));
		$this->assertContains('b', $query->getParams());
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT * FROM table WHERE a = ? OR c = ? AND e = f", 
			(string) $query->select()->from('table')->where(array('a' => 'b'))->orWhere('c = ?', 'd')->where('e = f'));
		$this->assertContains('b', $query->getParams());
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT * FROM table WHERE a = b", 
			(string) $query->select()->from('table')->where('a = ?', $query->expr('b')));
		$this->assertEquals(0, count($query->getParams()));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT * FROM table WHERE a = b", 
			(string) $query->select()->from('table')->where(array('a' => $query->expr('b'))));
		$this->assertEquals(0, count($query->getParams()));
	}
	
	public function testGroupBy()
	{
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT a FROM table GROUP BY a", (string) $query->select('a')->from('table')->groupBy('a'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT a FROM table GROUP BY a, b", (string) $query->select('a')->from('table')->groupBy('a', 'b'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT a FROM table GROUP BY a, b", (string) $query->select('a')->from('table')->groupBy(array('a', 'b')));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT a FROM table GROUP BY a HAVING a > 1", 
			(string) $query->select('a')->from('table')->groupBy('a')->having('a > 1'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT a FROM table GROUP BY a HAVING a = ?", 
			(string) $query->select('a')->from('table')->groupBy('a')->having('a = ?', 1));
		$this->assertContains(1, $query->getParams());
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT a, b FROM table GROUP BY a, b HAVING a > 1 OR b = 2", 
			(string) $query->select('a', 'b')->from('table')->groupBy('a', 'b')->having('a > 1')->orHaving('b = 2'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT a FROM table WHERE b = ? GROUP BY a HAVING a = ?", 
			(string) $query->select('a')->from('table')->where('b = ?', 2)->groupBy('a')->having('a = ?', 1));
		$this->assertEquals(array(2, 1), $query->getParams());
	}
	
	public function testOrderBy()
	{
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT a FROM table ORDER BY a", (string) $query->select('a')->from('table')->orderBy('a'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT a FROM table ORDER BY a DESC", (string) $query->select('a')->from('table')->orderBy('a', 'DESC'));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT a, b FROM table ORDER BY a, b DESC", 
			(string) $query->select('a', 'b')->from('table')->orderBy(array('a', 'b' => 'DESC')));
	}
	
	public function testLimit()
	{
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT * FROM table LIMIT 0, 10", (string) $query->select()->from('table')->limit(10));
		
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT * FROM table LIMIT 9, 10", (string) $query->select()->from('table')->limit(9, 10));
	}
	
	public function testDelete()
	{
		$query = new Atomik_Db_Query();
		$this->assertEquals("DELETE FROM table WHERE id = ?", (string) $query->delete()->from('table')->where('id = ?', 1));
		$this->assertContains(1, $query->getParams());
	}
	
	public function testInsert()
	{
		$query = new Atomik_Db_Query();
		$this->assertEquals("INSERT INTO table (f1, f2) VALUES (?, v2)", 
			(string) $query->insertInto('table')->values(array('f1' => 'v1', 'f2' => $query->expr('v2'))));
		$this->assertContains('v1', $query->getParams());
		
		$source = new Atomik_Db_Query();
		$this->assertEquals('SELECT * FROM source', (string) $source->select()->from('source'));
		$query = new Atomik_Db_Query();
		$this->assertEquals("INSERT INTO table SELECT * FROM source", (string) $query->insertInto('table')->values($source));
	}
	
	public function testUpdate()
	{
		$query = new Atomik_Db_Query();
		$this->assertEquals("UPDATE table SET f1 = ?, f2 = v2 WHERE id = ?", 
			(string) $query->update('table')->set(array('f1' => 'v1', 'f2' => $query->expr('v2')))->where('id = ?', 1));
		$this->assertEquals(array('v1', 1), $query->getParams());
	}
	
	public function testReset()
	{
		$query = new Atomik_Db_Query();
		$this->assertEquals("SELECT * FROM table1", (string) $query->select()->from('table1'));
		$query->reset();
		$this->assertEquals("DELETE FROM table2", (string) $query->delete()->from('table2'));
	}
}