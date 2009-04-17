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

/** Atomik_Model_Adapter_Local */
require_once 'Atomik/Model/Adapter/Local.php';

/**
 * Stores models in the session
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Adapter_Session extends Atomik_Model_Adapter_Local
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		@session_start();
		
		/* bind the _models property to the session array */
		if (!isset($_SESSION['__MODELS'])) {
			$_SESSION['__MODELS'] = array();
		}
		$this->_data = &$_SESSION['__MODELS'];
	}
}