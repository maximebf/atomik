<?php

/**
 * Parses a template
 */
class Atomik_Backend_Page
{
	/* default field types */
	protected $_types = array(
		'div' 		=> 'textarea',
		'p'			=> 'textarea',
		'span' 		=> 'input',
		'a'			=> 'link',
		'img' 		=> 'image',
		'h1' 		=> 'input',
		'h2' 		=> 'input',
		'h3' 		=> 'input',
		'h4' 		=> 'input',
		'h5' 		=> 'input',
		'h6' 		=> 'input'
	);
	
	/**
	 * @var array
	 */
	protected $_row;
	
	/**
	 * Current version
	 *
	 * @var int
	 */
	protected $_version;
	
	/**
	 * Fields
	 *
	 * @var array
	 */
	protected $_fields;
	
	/**
	 * Values
	 *
	 * @var array
	 */
	protected $_values = null;
	
	/**
	 * Language to use
	 *
	 * @var string
	 */
	protected $_lang = null;
	
	/**
	 * The regexp to match fields
	 *
	 * @var string
	 */
	protected $_regexp = '/(<(?P<tag>.+)\s(.*class="(.*atomik.*)".*)>)((?s).*)(<\/\k<tag>>)/U';
	
	/**
	 * Finds all editable pages
	 *
	 * @return Atomik_Backend_Page
	 */
	public static function getPagesFromDir($dirs = null)
	{
		if ($dirs === null) {
			$dirs = Atomik::get('plugins/Backend/templates_dir', array());
		}
		$pages = array();
		
		foreach (Atomik::path($dirs, true) as $dir) {
    		/* go through all files of the directory and loads
    		 * the one which are editable */
    		$iterator = new DirectoryIterator($dir);
    		foreach ($iterator as $file) {
    			/* avoir dotted or invisible files */
    			if (substr($file->getFilename(), 0, 1) == '.') {
    				continue;
    			}
    			
    			/* checks if it's a directory */
    			if ($file->isDir()) {
    			    $dir = self::getPagesFromDir($file->getPathname);
    			    if (count($dir)) {
    				    $pages[$file->getFilename()] = $dir; 
    			    }
    			}
    			
    			/* checks if it's editable */
				if (($page = self::fromFile($file->getPathname())) !== false) {
					$pages[] = $page;
				}
    		}
		}
		
		return $pages;
	}
	
	/**
	 * Loads a page using a filename
	 *
	 * @param string $filename
	 * @return Atomik_Backend_Page|bool False if not editable
	 */
	public static function fromFile($filename)
	{
	    $filename = realpath($filename);
		$content = file_get_contents($filename);
		
		/* checks if this page is editable */
		if (!preg_match('/class=".*atomik.*"/', $content)) {
		    return false;
		}
		
		/* checks if this page is already in the database */
		if (($row = Db::find('pages', array('filename' => $filename))) === false) {
		
    		/* gets page name */
    		if (preg_match('/<!--\s?Atomik:Page=(.+)-->/', $content, $match)) {
    		    $name = $match[1];
    		} else {
    		    /* the name is not specified in the page, use the filename (without extension) */
    		    $name = basename($filename);
    		    $name = substr($name, 0, strrpos($name, '.'));
    		}
		    
    		/* creates an entry for this page in the database */
		    $id = Db::insert('pages', array(
		        'filename' => $filename,
		        'version' => 1,
		        'name' => $name
		    ));
		    /* creates the first version */
		    Db::insert('pages_versions', array(
		        'page_id' => $id,
		        'version' => 1,
		        'note' => 'First version'
		    ));
		    
		    $row = Db::find('pages', array('id' => $id));
		}
		
		return new self($row, $content);
	}
	
	/**
	 * Loads a page using its id
	 *
	 * @param id $id
	 * @return Atomik_Backend_Page|bool False if not editable
	 */
	public static function fromId($id, $version = null)
	{
		/* checks the id match an entry in the database */
		if (($row = Db::find('pages', array('id' => $id))) === false) {
		    return false;
		}
		
		$content = file_get_contents($row['filename']);
		
		return new self($row, $content, $version);
	}
	
	/**
	 * Constructor
	 *
	 * @param string $content
	 */
	protected function __construct($row, $content, $version = null)
	{
	    $this->_content = $content;
	    $this->_row = $row;
	    $this->_version = $version === null ? $row['version'] : $version;
	}
	
	/**
	 * Returns the id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->_row['id'];
	}
	
	/**
	 * Returns the filename
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->_row['filename'];
	}
	
	/**
	 * Returns the page name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_row['name'];
	}
	
	/**
	 * Edit the age in a new language
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
	    $query = 'select lang from ' . Db::$config['prefix'] . 'pages_fields where page_id=? and version=? group by lang';
	    
	    $stmt = Db::query($query, array($this->_row['id'], $this->_version));
	    while ($row = $stmt->fetch()) {
	        $languages[] = $row['lang'];
	    }
	    
	    if (!in_array($this->_lang, $languages)) {
	        $languages[] = $this->_lang;
	    }
	    
	    return $languages;
	}
	
	/**
	 * Sets the version to use
	 *
	 * @param int $version
	 */
	public function setVersion($version)
	{
    	Db::update('pages', array('version' => $version), array('id' => $this->_row['id']));
    	$this->_version = $version;
	    $this->_row['version'] = $version;
	    $this->_values = null;
	}
	
	/**
	 * Returns the current version
	 *
	 * @return int
	 */
	public function getVersion()
	{
		return $this->_version;
	}
	
	/**
	 * Gets current version info (i.e. notes)
	 *
	 * @return string
	 */
	public function getVersionInfo()
	{
	    $row = Db::find('pages_versions', array(
	    	'page_id' => $this->_row['id'], 
	    	'version' => $this->_version
	    ));
	    
	    return $row['note'];
	}
	
	/**
	 * Gets all versions of the page
	 *
	 * @return array
	 */
	public function getVersions()
	{
	    $stmt = Db::findAll('pages_versions', array('page_id' => $this->_row['id']), 'id DESC');
	    return $stmt->fetchAll();
	}
	
	/**
	 * Creates a new version of the page
	 *
	 * @param string $note
	 * @return int The new version
	 */
	public function createNewVersion($note = '')
	{
	    /* select the higher version of the page */
	    $query = 'select max(version) from ' . Db::$config['prefix'] . 'pages_versions where page_id=?';
	    $max = Db::query($query, array($this->_row['id']))->fetchColumn();
	    
	    /* creates the new version */
	    $success = Db::insert('pages_versions', array(
	        'page_id' => $this->_row['id'],
	        'version' => ++$max,
	        'note' => $note
	    ));
	    
	    if ($success === false) {
	        return false;
	    }
	    
	    /* updates page version to the new one */
    	Db::update('pages', array('version' => $max), array('id' => $this->_row['id']));
    	$this->update($this->_row['name'], $this->_getValuesFromDb());
    	$this->_version = $max;
	    $this->_row['version'] = $max;
	    $this->_values = null;
	    
	    return $max;
	}
	
	/**
	 * Gets fields defined from the file
	 *
	 * @return array
	 */
	public function getFields()
	{
		/* checks if fields have been discovered */
		if ($this->_fields === null) {
			$this->_fields = array();
		
			/* builds the fields array */
			preg_match_all($this->_regexp, $this->_content, $matches);
			for ($i = 0, $count = count($matches[0]); $i < $count; $i++) {
				/* retreives the field id */
				if (!preg_match('/.*(id="(.*)").*/U', $matches[3][$i], $matchId)) {
					continue;
				}
				$id = $matchId[2];
			
				/* retreives the label */
				if (preg_match('/.*(title="(.*)").*/U', $matches[3][$i], $matchTitle)) {
					$label = $matchTitle[2];
				} else {
					$label = ucfirst($id);
				}
			
				/* type of field */
				if (preg_match('/.*(atomik-([a-z]+)).*/', $matches[4][$i], $matchType)) {
					$type = $matchType[2];
				} else {	
					$type = 'input';
					if (isset($this->_types[$matches['tag'][$i]])) {
						$type = $this->_types[$matches['tag'][$i]];
					}
				}
			
				/* saves the field */
				$this->_fields[$id] = array(
					'id' => $id,
					'type' => $type,
					'label' => $label,
					'value' => trim($matches[5][$i])
				);
			}
			
			/* retreives actual fields values */
			if (count($this->_fields)) {
				foreach ($this->_getValuesFromDb() as $id => $value) {
					$this->_fields[$id]['value'] = $value;
				}
			}
		}
		
		return $this->_fields;
	}
	
	/**
	 * Render the template
	 * 
	 * @return string
	 */
	public function render()
	{
		$content = $this->_content;
		
		/* retreives actual values from the db */
		$values = $this->_getValuesFromDb();
		
		preg_match_all($this->_regexp, $content, $matches);
		for ($i = 0, $count = count($matches[0]); $i < $count; $i++) {
			/* retreives the id */
			if (!preg_match('/.*(id="(.*)").*/U', $matches[3][$i], $matchId)) {
				continue;
			}
			$id = $matchId[2];
			
			/* checks if there is the value in the db */
			if (!isset($values[$id])) {
				continue;
			}
			
			/* replaces */
			$new = $matches[1][$i] . $values[$id] . $matches[6][$i];
			$content = str_replace($matches[0][$i], $new, $content);
		}
		
		return $content;
	}
	
	/**
	 * Update fields value
	 *
	 * @param array $fields
	 */
	public function update($name, $fields)
	{
	    $lang = $this->getLanguage();
		$actualFields = $this->_getValuesFromDb();
		
		/* saves fields values in the database */
		foreach ($fields as $field => $value) {
			if (!isset($actualFields[$field])) {
				/* creates a new entry for the field */
				Db::insert('pages_fields', array(
					'page_id' => $this->_row['id'],
					'field_name' => $field,
					'value' => $value,
				    'version' => $this->_version,
				    'lang' => $lang
				));
			} else {
				/* updates the existing entry */
				Db::update('pages_fields', array('value' => $value), array(
					'page_id' => $this->_row['id'],
					'field_name' => $field,
				    'version' => $this->_version,
				    'lang' => $lang
				));
			}
		}
		
		if ($name != $this->_row['name']) {
		    Db::update('pages', array('name' => $name), array('id' => $this->_row['id']));
		    $this->_row['name'] = $name;
		}
	}
	
	/**
	 * Fetches all fields values for the current template
	 *
	 * @return array
	 */
	protected function _getValuesFromDb()
	{
	    /* checks if we have already fetch values */
	    if ($this->_values !== null) {
	        return $this->_values;
	    }
	    
		/* finds all fields for the current filename */
		$stmt = Db::findAll('pages_fields', array(
			'page_id' => $this->_row['id'], 
			'version' => $this->_version,
		    'lang' => $this->getLanguage()
	    ));
		
		/* fetches values */
		$this->_values = array();
		while ($row = $stmt->fetch()) {
			$this->_values[$row['field_name']] = $row['value'];
		}
		
		return $this->_values;
	}
}
