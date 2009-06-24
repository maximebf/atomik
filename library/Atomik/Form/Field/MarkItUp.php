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
 * @subpackage Form
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Form_Field_Textarea */
require_once 'Atomik/Form/Field/Textarea.php';

/** Atomik_Assets */
require_once 'Atomik/Assets.php';

/**
 * @package Atomik
 * @subpackage Form
 */
class Atomik_Form_Field_MarkItUp extends Atomik_Form_Field_Textarea
{
	protected $_config = array();
	
	protected static $_defaultConfig = array();
	
	public static function setDefaultConfig($config)
	{
		self::$_defaultConfig = $config;
	}
	
	public static function getDefaultConfig()
	{
		return self::$_defaultConfig;
	}
	
	protected function _init()
	{
		Atomik_Assets::addNamedAsset('markitup');
	}
	
	public function setConfig($config)
	{
		$this->_config = array_merge_recursive(self::$_defaultConfig, $config);
	}
	
	public function getConfig()
	{
		return $this->_config;
	}
	
	public function render()
	{
		$html = parent::render();
		$html .= sprintf('<script type="text/javascript">jQuery(function($) { $("#%s").markItUp(%s); });</script>', 
			$this->getId(), json_encode($this->_config));
		return $html;
	}
}