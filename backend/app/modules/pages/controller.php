<?php

/* Atomik_TemplateParser */
Atomik::needed('Atomik/Backend/Page');
	
/**
 * Pages manager
 */
class PagesController
{
	/**
	 * List all editable templates
	 */
	public function index($request)
	{
		$this->pages = Atomik_Backend_Page::getPagesFromDir();
	}
	
	/**
	 * Edit a template
	 */
	public function edit($request)
	{
		/* retreives the file real path and checks if the file exists */
		if (!isset($_GET['id']) || empty($_GET['id'])) {
			Atomik::redirect('pages/index');
		}
		
		if (isset($_GET['version'])) {
		    $version = (int) $_GET['version'];
		} else {
		    $version = null;
		}
		
		/* parses the template to retreives fields */
		if (($this->page = Atomik_Backend_Page::fromId((int) $_GET['id'], $version)) === false) {
			SessionPlugin::flash('The page is not editable', 'error');
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
		if (!isset($_POST['id']) || empty($_POST['id']) || 
		    !isset($_POST['name']) || empty($_POST['name']) || 
			!isset($_POST['fields']) || !is_array($_POST['fields'])) {
				Atomik::redirect('pages/index');	
		}
		
		$pageId = $_POST['id'];
		$fields = $_POST['fields'];
		$name = $_POST['name'];
		
		if (isset($_POST['version'])) {
		    $version = (int) $_POST['version'];
		} else {
		    $version = null;
		}
		
		/* gets the page */
		if (($page = Atomik_Backend_Page::fromId($pageId, $version)) === false) {
			SessionPlugin::flash('The page do not exists', 'error');
			Atomik::redirect('pages/index');	
		}
		
		/* updates fields values */
		$page->update($name, $fields);
		
		/* success */
		SessionPlugin::flash('Saved successfuly!', 'success');
		Atomik::redirect('pages/edit?id=' . $page->getId() . '&version=' . $page->getVersion());
	}
	
	public function createVersion()
	{
		if (!isset($_POST['id']) || empty($_POST['id']) || !isset($_POST['note'])) {
			Atomik::redirect('pages/index');	
		}
		
		$pageId = $_POST['id'];
		$note = $_POST['note'];
		
		/* gets the page */
		if (($page = Atomik_Backend_Page::fromId($pageId)) === false) {
			SessionPlugin::flash('The page do not exists', 'error');
			Atomik::redirect('pages/index');	
		}
		
		$newVersion = $page->createNewVersion($note);
		
		/* success */
		SessionPlugin::flash('Version created successfuly!', 'success');
		Atomik::redirect('pages/edit?id=' . $page->getId() . '&version=' . $newVersion);
	}
}
