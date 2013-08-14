<?php

namespace Atomik\DebugBar;

use DebugBar\DataCollector\PDO\TraceablePDO;
use Atomik\Db;

class TraceableDb extends TraceablePDO
{
    public function __construct(Db $db)
    {
        parent::__construct($db);
    }

    /**
     * Executes a SELECT statement and returns the PDOStatement object
     * 
     * @param string $query
     * @param string $columns
     * @param array $where
     * @param string $afterWhere
     * @return PDOStatement
     */
    public function executeSelect($tableName, $columns = '*', $where = null, $afterWhere = '')
    {
        return $this->pdo->executeSelect($tableName, $columns, $where, $afterWhere);
    }

    /**
     * Executes a SELECT * on $tableName and returns all rows as an array
     * 
     * @param string $query
     * @param array $where
     * @param string $afterWhere
     * @return array
     */
    public function select($tableName, $where = null, $afterWhere = '')
    {
        return $this->pdo->select($tableName, $where, $afterWhere);
    }

    /**
     * Executes a SELECT * on $tableName and returns the first row
     * 
     * @param string $query
     * @param array $where
     * @param string $afterWhere
     * @return array
     */
    public function selectOne($tableName, $where = null, $afterWhere = '')
    {
        return $this->pdo->selectOne($tableName, $where, $afterWhere);
    }
    
    /**
     * Executes a SELECT on $tableName and returns the first column of the first row
     * 
     * @param string $query
     * @param string $column
     * @param array $where
     * @return mixed
     */
    public function selectValue($tableName, $column, $where = null)
    {
        return $this->pdo->selectValue($tableName, $column, $where);
    }
    
    /**
     * Executes a SELECT COUNT(*) on $tableName
     * 
     * @param string $tableName
     * @param array|string $where
     * @return int
     */
    public function count($tableName, $where = null)
    {
        return $this->pdo->count($tableName, $where);
    }
    
    /**
     * Inserts some data into the specified table
     * 
     * @param string $tableName
     * @param array $data
     * @return Statement
     */
    public function insert($tableName, array $data)
    {
        return $this->pdo->insert($tableName, $data);
    }
    
    /**
     * Updates the specified table matching the $where using $data
     * 
     * @param string $tableName
     * @param array $data
     * @param array|string $where
     * @return Statement
     */
    public function update($tableName, array $data, $where = null)
    {
        return $this->pdo->update($tableName, $data, $where);
    }
    
    /**
     * Deletes row from a table
     * 
     * @param string $tableName
     * @param array|string $where
     * @return Statement
     */
    public function delete($tableName, $where = null)
    {
        return $this->pdo->delete($tableName, $where);
    }
}