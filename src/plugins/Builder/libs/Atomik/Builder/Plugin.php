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
 * Builds a plugin
 *
 * @package Atomik
 * @subpackage Builder
 */
class Atomik_Builder_Plugin
{
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var boolean
	 */
	public $useClass = true;
	
	/**
	 * @var boolean
	 */
	public $useDirectory = false;
	
	/**
	 * @var Atomik_Manifest
	 */
	public $manifest;
	
	/**
	 * @var array
	 */
	public $config = array();
	
	/**
	 * @var array
	 */
	public $listeners = array();
	
	/**
	 * Path to events xml description
	 * 
	 * @var SimpleXMLElement
	 */
	public $eventsXml = 'http://www.atomikframework.com/docs/events/events-api.xml';
	
	/**
	 * Events (will be automatically loaded using the eventsXml)
	 * 
	 * @var SimpleXMLElement
	 */
	public $events;
	
	/**
	 * Builds the plugin and creates a zip with the specified filename
	 * 
	 * @param string $filename Filename of the resulting zip file
	 */
	public function build($filename)
	{
		$this->name = ucfirst($this->name);
		
		// load events if necessary
		if ($this->events === null) {
			$this->events = simplexml_load_file($this->eventsXml);
		}
		
		// listeners
		$listeners = array();
		foreach ($this->listeners as $listener) {
			$args = array();
			foreach ($this->events->event as $event) {
				if ($event['name'] != $listener) {
					continue;
				}
				foreach ($event->children() as $name => $param) {
					if ($name == 'description') continue;
					$args[] = ($name == 'refparam' ? '&' : '') . $param['name'];
				}
				break;
			}
			$listeners[$listener] = array(
				'args' => implode(', ', $args),
				'node' => $event
			);
		}
		$this->listeners = $listeners;
		
		// plugin's php file
		$template = 'Atomik/Builder/Plugin/' . ($this->useClass ? 'Class.php' : 'Simple.php');
		ob_start();
		include $template;
		$content = ob_get_clean();
		
		$zip = new ZipArchive();
		$zip->open($filename, ZipArchive::CREATE);
		
		// manifest
		if ($this->manifest !== null) {
			$zip->addFromString('Manifest.xml', $this->manifest->toXml());
		}
		
		// adding files to the zip
		if ($this->useDirectory) {
			$zip->addEmptyDir($this->name);
			$zip->addEmptyDir($this->name . '/libraries');
			$zip->addFromString($this->name . '/Plugin.php', $content);
		} else {
			$zip->addFromString($this->name . '.php', $content);
		}
		
		$zip->close();
	}
}
