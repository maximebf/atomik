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

/** Atomik_Model_EventListener */
require_once 'Atomik/Model/EventListener.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_EventDispatcher
{
	/** @var array of Atomik_Model_EventListener */
	protected $_listeners = array();
	
	/**
	 * @param Atomik_Model_EventListener $listener
	 */
	public function addListener(Atomik_Model_EventListener $listener)
	{
	    $this->_listeners[] = $listener;
	}
	
	/**
	 * @param Atomik_Model_Descriptor $descriptor
	 * @param string $event
	 * @param array $args
	 */
	public function notify($event, Atomik_Model_Descriptor $descriptor)
	{
	    $args = func_get_args();
	    array_shift($args);
	    
		foreach ($this->_listeners as $listener) {
			call_user_func_array(array($listener, $event), $args);
		}
	}
}