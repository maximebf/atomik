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

/** Atomik_Db_Script_Interface */
require_once 'Atomik/Db/Script/Interface.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Script implements Atomik_Db_Script_Interface
{
	/**
	 * @var Atomik_Db_Script_Output_Interface
	 */
	protected $_outputHandler;
	
	/**
	 * @var array
	 */
	protected $_scripts = array();
	
	/**
	 * @var array
	 */
	protected $_sqlCommands = array();
	
	/**
	 * Constructor
	 * 
	 * @param 	Atomik_Db_Script_Output_Interface	$outputHandler
	 */
	public function __construct(Atomik_Db_Script_Output_Interface $outputHandler = null)
	{
		$this->setOutputHandler($outputHandler);
	}
	
	/**
	 * Sets the output handler
	 * 
	 * @param 	Atomik_Db_Script_Output_Interface	$outputHandler
	 */
	public function setOutputHandler(Atomik_Db_Script_Output_Interface $outputHandler = null)
	{
		if ($outputHandler === null) {
			$outputHandler = new Atomik_Db_Script_Output_Dummy();
		}
		$this->_outputHandler = $outputHandler;
	}
	
	/**
	 * Returns the output handler
	 * 
	 * @return Atomik_Db_Script_Output_Interface
	 */
	public function getOutputHandler()
	{
		return $this->_outputHandler;
	}
	
	/**
	 * Adds many scripts at once
	 * 
	 * @param	array	$scripts
	 */
	public function addScripts($scripts)
	{
		foreach ($scripts as $script) {
			$this->addScript($script);
		}
	}
	
	/**
	 * Adds a script to execute
	 * 
	 * @param	Atomik_Db_Script_Interface	$script
	 */
	public function addScript(Atomik_Db_Script_Interface $script)
	{
		$this->_scripts[] = $script;
	}
	
	/**
	 * Creates a Atomik_Db_Script_Text object and adds it
	 * 
	 * @param	string	$sql
	 * @return 	Atomik_Db_Script_Text
	 */
	public function addTextScript($sql)
	{
		$script = new Atomik_Db_Script_Text($sql);
		$this->_scripts[] = $script;
		return $script;
	}
	
	/**
	 * Removes a script
	 * 
	 * @param	Atomik_Db_Script_Interface	$script
	 */
	public function removeScript(Atomik_Db_Script_Interface $script)
	{
		for ($i = 0, $c = count($this->_scripts); $i < $c; $i++) {
			if ($this->_scripts[$i] == $script) {
				unset($this->_scripts[$i]);
				return;
			}
		}
	}
	
	/**
	 * Returns all scripts
	 * 
	 * @return array
	 */
	public function getScripts()
	{
		return $this->_scripts;
	}
	
	/**
	 * Returns the sql for the all the added scripts
	 * 
	 * @return string
	 */
	public function getSql()
	{
		$sql = '';
		foreach ($this->_scripts as $script) {
			$sql .= $script->getSql();
		}
		return $sql;
	}
	
	/**
	 * Executes the sql against the specified db instance
	 * 
	 * @param	Atomik_Db_Instance	$db
	 * @return	bool	success
	 */
	public function run(Atomik_Db_Instance $db)
	{
		foreach ($this->_scripts as $script) {
			$this->_outputHandler->executingScript($script);
			$sql = $script->getSql();
			
			try {
				$stmt = $db->prepare($sql);
				if (!$stmt->execute()) {
					$errorInfo = $stmt->errorInfo();
					$this->_outputHandler->error($errorInfo[2]);
					return false;
				}
			} catch (Exception $e) {
				$this->_outputHandler->error($e->getMessage());
				return false;
			}
		}
		return true;
	}
}