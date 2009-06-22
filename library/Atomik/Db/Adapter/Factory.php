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

/** Atomik_Db_Adapter_Interface */
require_once 'Atomik/Db/Adapter/Interface.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Adapter_Factory
{
	/**
	 * Creates an instance of an adapter
	 * 
	 * @param 	string|objet 	$name		The last part of the adapter name if it starts with Atomik_Db_Adapter_ or a class name
	 * @return 	Atomik_Db_Adapter_Interface
	 */
	public static function factory($name, PDO $pdo)
	{
		$className = 'Atomik_Db_Adapter_' . ucfirst(strtolower($name));
		if (!class_exists($className)) {
			$className = $name;
			if (!class_exists($className)) {
				$className = 'Atomik_Db_Adapter';
			}
		}
		
		return new $className($pdo);
	}
}