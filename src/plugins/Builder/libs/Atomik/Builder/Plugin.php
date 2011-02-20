<?php
/**
 * Atomik Framework
 * Copyright (c) 2008-2009 Maxime Bouroumeau-Fuseau
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Atomik
 * @subpackage Builder
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
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