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
 * @subpackage Db
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Db_Script_Output_Interface */
require_once 'Atomik/Db/Script/Output/Interface.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Script_Output_Text implements Atomik_Db_Script_Output_Interface 
{
	/**
	 * @var bool
	 */
	public $echo = true;
	
	/**
	 * Constructor
	 * 
	 * @param	bool	$echo	Whether to echo or not
	 */
	public function __construct($echo = true)
	{
		$this->echo = $echo;
	}
	
	/**
	 * Returns the output
	 * 
	 * @return string
	 */
	public function getText()
	{
		return $this->_text;
	}
	
	/**
	 * @see Atomik_Db_Script_Output_Text::getText()
	 */
	public function __toString()
	{
		return $this->getText();
	}
	
	/**
	 * @see Atomik_Db_Script_Output_Interface::executingScript()
	 */
	public function executingScript($script)
	{
		switch(get_class($script)) {
			case 'Atomik_Db_Script_Text':
				$this->_println('Executing some SQL commands');
				break;
			case 'Atomik_Db_Script_File':
				$this->_println('Executing SQL commands from the file ' . $script->filename);
				break;
			case 'Atomik_Db_Script_Model':
				$this->_println('Creating table for model ' . $script->getModelBuilder()->name);
				break;
		}
	}
	
	/**
	 * @see Atomik_Db_Script_Output_Interface::error()
	 */
	public function error($message)
	{
		$this->_println('ERROR: ' . $message);
	}
	
	/**
	 * Adds a string to $_text and echo it if enabled
	 * 
	 * @param	string	$text
	 */
	protected function _println($text)
	{
		$this->_text .= $text . "\n";
		if ($this->echo) {
			echo $text . "\n";
		}
	}
}