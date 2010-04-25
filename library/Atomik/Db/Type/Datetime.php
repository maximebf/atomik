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

/** Atomik_Db_Type_Abstract */
require_once 'Atomik/Db/Type/Abstract.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Type_Datetime extends Atomik_Db_Type_Abstract
{
	/**
	 * @see Atomik_Db_Types_Abstract::getSqlType()
	 */
	public function getSqlType()
	{
		return 'DATETIME';
	}
	
	/**
	 * @see Atomik_Db_Types_Abstract::filterInput()
	 */
	public function filterInput($input)
	{
	    if ($input === null) {
	        return null;
	    }
		return new DateTime($input);
	}
	
	/**
	 * @see Atomik_Db_Types_Abstract::filterOutput()
	 */
	public function filterOutput($output) 
	{
	    if (empty($output)) {
	        return null;
	    }
	    
	    if (is_string($output)) {
	        $output = strtotime($output);
	    }
	    
	    if ($output instanceof DateTime) {
	        $output = $output->format('Y-m-d H:i:s');
	    } else if (is_int($output)) {
	        $output = date('Y-m-d H:i:s', $output);
	    }
		return $output;
	}
}