<?php

/**
 * Parses a template
 */
class Atomik_Template_Parser
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
	 * Filename
	 *
	 * @var string
	 */
	protected $_filename;
	
	/**
	 * Is editable
	 *
	 * @var bool
	 */
	protected $_editable;
	
	/**
	 * Name
	 *
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Fields
	 *
	 * @var array
	 */
	protected $_fields;
	
	/**
	 * The regexp to match fields
	 *
	 * @var string
	 */
	protected $_regexp = '/(<(?P<tag>.+)\s(.*class="(.*atomik.*)".*)>)((?s).*)(<\/\k<tag>>)/U';
	
	/**
	 * Database table name
	 *
	 * @var string
	 */
	protected $_table;
	
	/**
	 * Finds all supported templates and load them
	 *
	 * @return Atomik_Template_Parser
	 */
	public static function getTemplatesFromDir($dir = null)
	{
		if ($dir === null) {
			$dir = Atomik::get('backend/templates_dir');
		}
		
		/* go through all files of the template directory and loads
		 * the one which are editable */
		$templates = array();
		$iterator = new DirectoryIterator($dir);
		foreach ($iterator as $file) {
			/* avoir dotted or invisible files */
			if (substr($file->getFilename(), 0, 1) == '.') {
				continue;
			}
			
			/* checks if it's a directory */
			if ($file->isDir()) {
				$templates = array_merge($templates, 
					self::getSupportedTemplates( $file->getPathname));
			}
			
			/* checks if it's editable */
			$content = file_get_contents($file->getPathname());
			if (preg_match('/<!--\s?Atomik:Page=(.+)\s?-->/', $content)) {
				$template = new self($content, $file->getPathname());
				/* checks if the template type match */
				if ($type === null || $template->isEditable()) {
					$templates[] = $template;
				}
			}
		}
		return $templates;
	}
	
	/**
	 * Loads a template using a filename
	 *
	 * @param string $filename
	 * @return Atomik_TemplateParser
	 */
	public static function fromFile($filename)
	{
		$content = file_get_contents($filename);
		return new self($content, $filename);
	}
	
	/**
	 * Constructor
	 *
	 * @param string $content
	 */
	public function __construct($content, $filename = null)
	{
		$this->_content = $content; 
		$this->_filename = $filename;
		$this->_editable = false;
		$this->_table = Atomik::get('backend/db_prefix') . 'content';
		
		/* checks if the template is editable */
		if (preg_match('/<!--\s?Atomik:Page=(.+)\s?-->/', $content, $match)) {
			$this->_editable = true;
			$this->_name = $match[1];
		}
	}
	
	/**
	 * Returns the filename
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->_filename;
	}
	
	/**
	 * Gets the type
	 *
	 * @return int
	 */
	public function isEditable()
	{
		return $this->_editable;
	}
	
	/**
	 * Returns the template name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Gets fields defined in the template
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
				/* retreives the id */
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
					'value' => trim($matches[5][$i]),
					'new' => true
				);
			}
			
			/* retreives actual fields values */
			if (count($this->_fields)) {
				foreach ($this->_getValuesFromDb() as $id => $value) {
					$this->_fields[$id]['value'] = $value;
					$this->_fields[$id]['new'] = false;
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
	 * Fetches all fields values for the current template
	 *
	 * @return array
	 */
	protected function _getValuesFromDb()
	{
		/* finds all fields for the current filename */
		$stmt = Db::findAll($this->_table, array('template' => $this->_filename));
		
		/* fetches values */
		$values = array();
		while ($row = $stmt->fetch()) {
			$values[$row['content_id']] = $row['data'];
		}
		
		return $values;
	}
}
