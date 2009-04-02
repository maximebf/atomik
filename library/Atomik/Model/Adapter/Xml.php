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

/** Atomik_Model_Adapter_Interface */
require_once 'Atomik/Model/Adapter/Interface.php';

/**
 * XML Model Adapter
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Adapter_Xml implements Atomik_Model_Adapter_Interface
{
	/**
	 * Query the adapter
	 * 
	 * @param	Atomik_Model_Query	$query
	 * @return 	Atomik_Model_Modelset
	 */
	public function query(Atomik_Model_Query $query)
	{
		
	}
	
	/**
	 * 
	 */
	public function save(Atomik_Model $model)
	{
		
	}
	
	/**
	 * 
	 */
	public function delete(Atomik_Model $model)
	{
		
	}
}