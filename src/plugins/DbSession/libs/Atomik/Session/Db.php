<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package Atomik
 * @subpackage Session
 */
class Atomik_Session_Db
{
    /** @var Atomik_Db_Instance */
    protected $_db;
    
    /** @var int */
    protected $_lifetime;
    
    /** @var string */
    protected $_table;
    
    /** @var string */
    protected $_idColumn;
    
    /** @var string */
    protected $_dataColumn;
    
    /** @var string */
    protected $_expiresColumn;
    
    /**
     * @param Atomik_Session_Db $handler
     */
    public static function register(Atomik_Session_Db $handler = null)
    {
        if ($handler === null) {
            $handler = new Atomik_Session_Db(Atomik_Db::getInstance());
        }
        
        session_set_save_handler(
        	array($handler, 'open'), 
        	array($handler, 'close'),
        	array($handler, 'read'), 
        	array($handler, 'write'), 
        	array($handler, 'destroy'), 
        	array($handler, 'gc'));
    }
    
    /**
     * @param Atomik_Db_Instance $db
     * @param string $table
     * @param string $idColumn
     * @param string $dataColumn
     * @param string $expiresColumn
     */
    public function __construct(Atomik_Db_Instance $db, $table = 'sessions', $idColumn = 'session_id', 
        $dataColumn = 'session_data', $expiresColumn = 'session_expires')
    {
        $this->_db = $db;
        $this->_table = $table;
        $this->_idColumn = $idColumn;
        $this->_dataColumn = $dataColumn;
        $this->_expiresColumn = $expiresColumn;
    }
    
    public function __desctruct()
    {
        session_write_close();
    }
    
    /**
     * @param int $lifetime
     */
    public function setLifetime($lifetime)
    {
        $this->_lifetime = (int) $lifetime;
    }
    
    /**
     * @return int
     */
    public function getLifetime()
    {
        return $this->_lifetime;
    }
    
    /* ---------------------------------------------------------------------
     * Session handler methods
     */
    
    public function open($save_path, $session_name)
    {
        $this->_lifetime = (int) ini_get('session.gc_maxlifetime');
        return true;
    }
    
    public function close()
    {
        return true;
    }
    
    public function read($id)
    {
        return (string) $this->_db->findValue($this->_table, $this->_dataColumn, $this->_getWhere($id));
    }
    
    public function write($id, $sessionData)
    {
        $data = array(
            $this->_idColumn => $id,
            $this->_dataColumn => $sessionData,
            $this->_expiresColumn => time() + $this->_lifetime
        );
        return $this->_db->set($this->_table, $data, $this->_getWhere($id));
    }
    
    public function destroy($id)
    {
        return $this->_db->delete($this->_table, $this->_getWhere($id));
    }
    
    public function gc($maxlifetime)
    {
        $cond = sprintf('%s < %s', $this->_expiresColumn, time());
        return $this->_db->delete($this->_table, $cond);
    }
    
    protected function _getWhere($id)
    {
        return array($this->_idColumn => $id);
    }
}
