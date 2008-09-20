<?php

/**
 * Parses a template
 */
class Atomik_Backend_Record
{
	/**
	 * Database table
	 *
	 * @var string
	 */
	protected $_table = 'records';
	
	/**
	 * Foreign key
	 *
	 * @var string
	 */
	protected $_foreignKey = 'record_id';
	
	/**
	 * @var int
	 */
	protected $_id;
	
	/**
	 * @var int
	 */
	protected $_modelId;
	
	/**
	 * @var array
	 */
	protected $_data;
	
	/**
	 * Language to use
	 *
	 * @var string
	 */
	protected $_lang = null;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
	}
	
	/**
	 * Fetch a record using its id
	 *
	 * @param int $id
	 */
	public function fromId($id)
	{
		/* checks the id match an entry in the database */
		if (($row = Db::find($this->_table, array('id' => $id))) === false) {
		    return false;
		}
		
		$this->_id = $row['id'];
		$this->_modelId = $row['model_id'];
	}
	
	/**
	 * Returns the id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->_id;
	}
	
	/**
	 * Returns the model id
	 *
	 * @return int
	 */
	public function getModelId()
	{
		return $this->_modelId;
	}
	
	/**
	 * Edit the page in a different language
	 *
	 * @param string $lang
	 */
	public function setLanguage($lang = null)
	{
	    if ($lang !== null) {
	        $this->_lang = $lang;
	        return;
	    }
	    
        /* checks if the lang plugin is loaded */    	    
	    if (Atomik::isPluginLoaded('lang')) {
	        $this->_lang = LangPlugin::$config['language'];
	        return;
	    }
	    
	    /* checks for the default_language config key */
	    $this->_lang = Atomik::get('plugins/Backend/default_language', 'en');
	}
	
	/**
	 * Get the current page language
	 *
	 * @return string
	 */
	public function getLanguage()
	{
	    if ($this->_lang === null) {
	        $this->setLanguage();
	    }
	    
	    return $this->_lang;
	}
	
	/**
	 * Get all languages in which the page is available
	 *
	 * @return array
	 */
	public function getLanguages()
	{
	    $languages = array();
	    $query = 'select lang from ' . Db::$config['prefix'] . $this->_table 
	    	   . '_fields where ' . $this->_foreignKey . '=? group by lang';
	    
	    $stmt = Db::query($query, array($this->_id));
	    while ($row = $stmt->fetch()) {
	        $languages[] = $row['lang'];
	    }
	    
	    if (!in_array($this->_lang, $languages)) {
	        $languages[] = $this->_lang;
	    }
	    
	    return $languages;
	}
	
	/**
	 * Update fields value
	 *
	 * @param array $data
	 */
	public function setData($data)
	{
	    $lang = $this->getLanguage();
		$this->getData();
		
		/* saves fields values in the database */
		foreach ($data as $field => $value) {
			if (!isset($this->_data[$field])) {
				/* creates a new entry for the field */
				Db::insert($this->_table . '_fields', array(
					$this->_foreignKey => $this->_id,
					'field_name' => $field,
					'value' => $value,
				    'lang' => $lang
				));
			} else {
				/* updates the existing entry */
				Db::update($this->_table . '_fields', array('value' => $value), array(
					$this->_foreignKey => $this->_id,
					'field_name' => $field,
				    'lang' => $lang
				));
			}
		}
		
		$this->_data = array_merge($this->_data, $data);
	}
	
	/**
	 * Fetches all fields values for the current record
	 *
	 * @return array
	 */
	public function getData()
	{
	    /* checks if we have already fetch values */
	    if ($this->_data !== null) {
	        return $this->_data;
	    }
	    
		/* finds all fields for the current filename */
		$stmt = Db::findAll($this->_table . '_fields', array(
			$this->_foreignKey => $this->_id,
		    'lang' => $this->getLanguage()
	    ));
		
		/* fetches values */
		$this->_data = array();
		while ($row = $stmt->fetch()) {
			$this->_data[$row['field_name']] = $row['value'];
		}
		
		return $this->_data;
	}
}
