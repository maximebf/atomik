<?php

/* Atomik_TemplateParser */
Atomik::needed('Atomik/Template/Parser');
	
/**
 * Pages manager
 */
class PagesController
{
	/**
	 * Content table name
	 *
	 * @var string
	 */
	protected $_table;
	
	/**
	 * Before actions
	 */
	public function _before()
	{
		/* saves the full content table name */
		$this->_table = Atomik::get('backend/db_prefix') . 'content';
	}
	
	/**
	 * List all editable templates
	 */
	public function index($request)
	{
		$this->templates = Atomik_Template_Parser::getTemplatesFromDir();
	}
	
	/**
	 * Edit a template
	 */
	public function edit($request)
	{
		/* retreives the file real path and checks if the file exists */
		if (($this->file = @realpath($_GET['file'])) === false) {
			SessionPlugin::flash('File ' . $this->file . ' does not exists', 'error');
			Atomik::redirect('pages/index');
		}
		
		/* parses the template to retreives fields */
		$this->template = Atomik_Template_Parser::fromFile($this->file);
		
		/* checks if the template is editable */
		if (!$this->template->isEditable()) {
			SessionPlugin::flash('The file ' . $this->file . ' is not editable', 'error');
			Atomik::redirect('pages/index');
		}
	}
	
	/**
	 * Saves fields after edition
	 */
	public function save()
	{
		/* checks if there are post data */
		if (!count($_POST)) {
			Atomik::redirect('pages/index');
		}
		
		/* checks if all values are present */
		if (!isset($_POST['file']) || empty($_POST['file']) || 
			!isset($_POST['fields']) || !is_array($_POST['fields'])) {
				SessionPlugin::flash('An error occured', 'error');
				Atomik::redirect('pages/index');	
		}
		
		$filename = $_POST['file'];
		$fields = $_POST['fields'];
		$newFields = array();
		
		/* checks for new fields */
		if (isset($_POST['newFields'])) {
			if (!is_array($_POST['newFields'])) {
				SessionPlugin::flash('An error occured', 'error');
				Atomik::redirect('pages/index');
			}
			$newFields = $_POST['newFields'];
		}
		
		/* saves fields values in the database */
		foreach ($fields as $field => $value) {
			if (isset($newFields[$field])) {
				/* creates a new entry for the field */
				Db::insert($this->_table, array(
					'template' => $filename,
					'content_id' => $field,
					'data' => $value
				));
			} else {
				/* updates the existing entry */
				Db::update($this->_table, array('data' => $value), array(
					'template' => $filename,
					'content_id' => $field
				));
			}
		}
		
		/* success */
		SessionPlugin::flash('Saved successfuly!', 'success');
		Atomik::redirect('pages/edit?file=' . $filename);
	}
}
