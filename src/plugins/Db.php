<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atomik;

use Atomik;
use PDO;

class Db extends PDO
{
    public static $config = array();

    private static $instance;

    public static function start(&$config)
    {
        $config = array_merge(array(

            // connection string (see PDO)
            'dsn'               => false,
            
            // username
            'username'          => 'root',
            
            // password
            'password'          => '',

        ), $config);

        self::$config = &$config;
        self::$instance = new Db($config['dsn'], $config['username'], $config['password']);
        Atomik::set('db', self::$instance);
    }

    public function __construct($dsn, $username = null, $password = null, $driver_options = null)
    {
        parent::__construct($dsn, $username, $password, $driver_options);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
        list($where, $params) = $this->_buildWhere($where);
        $query = "SELECT $columns FROM $tableName $where $afterWhere";
        if (!($stmt = $this->prepare($query))) {
            return false;
        }
        $stmt->execute($params);
        return $stmt;
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
        $stmt = $this->executeSelect($tableName, '*', $where, $afterWhere);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $stmt = $this->executeSelect($tableName, '*', $where, $afterWhere);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row;
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
        $stmt = $this->executeSelect($tableName, $column, $where, $afterWhere);
        return $stmt->fetchColumn();
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
        return $this->selectValue($tableName, 'COUNT(*)', $where);
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
        $query = sprintf("INSERT INTO $tableName (%s) VALUES (%s)",
            implode(', ', array_keys($data)), 
            implode(', ', array_fill(0, count($data), '?'))
        );

        $stmt = $this->prepare($query);
        $stmt->execute(array_values($data));
        return $stmt;
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
        list($where, $params) = $this->_buildWhere($where);
        
        $set = array();
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
        }
        $query = "UPDATE $tableName SET " . implode(', ', $set) . " $where";
        $params = array_merge(array_values($data), $params);
        
        $stmt = $this->prepare($query);
        $stmt->execute($params);
        return $stmt;
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
        list($where, $params) = $this->_buildWhere($where);
        $query = "DELETE FROM $tableName $where";
        
        $stmt = $this->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Builds a condition string
     * 
     * If $where is empty, the returned string will be empty
     * If $where is a string, a WHERE $where string will be returned with no params
     * If $where is an array, each key, value pairs will be converted to a key = value condition
     * and some params will be returned
     * 
     * @param mixed $where
     * @return array (query, params)
     */
    protected function _buildWhere($where)
    {
        if (empty($where)) {
            return array('', array());
        }
        if (is_string($where)) {
            return array("WHERE $where", array());
        }
        return array(
            'WHERE ' . implode(' = ? AND ', array_keys($where)) . ' = ?',
            array_values($where)
        );
    }
}
