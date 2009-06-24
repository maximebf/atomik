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
 * @subpackage Model
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Model_Field_Abstract */
require_once 'Atomik/Model/Field/Abstract.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Field extends Atomik_Model_Field_Abstract
{
	/**
	 * @var string
	 */
	public $type;
	
	/**
	 * Constructor
	 * 
	 * @param	string	$name
	 * @param 	array	$options
	 */
	public function __construct($name, $type, $options = array())
	{
		parent::__construct($name, $options);
		$this->type = $type;
	}
	
	/**
	 * Returns an array where the first item is the sql type name and the second the length
	 * 
	 * @return array
	 */
	public function getSqlType()
	{
		return array($this->type, $this->getOption('length', null));
	}
}