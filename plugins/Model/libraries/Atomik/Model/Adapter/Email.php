<?php
/**
 * Atomik Framework
 * Copyright (c) 2008 Maxime Bouroumeau-Fuseau
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
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Model_Adapter_Interface */
require_once 'Atomik/Model/Adapter/Interface.php';

/** Atomik_Model */
require_once 'Atomik/Model.php';

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

/**
 * Email Adapter
 * 
 * Sends an email when saved
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Adapter_Email implements Atomik_Model_Adapter_Interface
{
	public function query(Atomik_Model_Builder $builder, $query)
	{
		return array();
	}
	
	public function findAll(Atomik_Model_Builder $builder, $where = null, $orderBy = '', $limit = '')
	{
		return array();
	}
	
	public function find(Atomik_Model_Builder $builder, $where, $orderBy = '', $limit = '')
	{
		return null;
	}
	
	public function save(Atomik_Model $model)
	{
		$builder = $model->getBuilder();
		if (($email = $builder->getOption('sendto')) === null) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('The sendto option must be '
				. 'specified when using the Email adapter');
		}
		
		$subject = null;
		$message = null;
		
		foreach ($builder->getFields() as $field) {
			if ($field->hasOption('subject')) {
				$subject = $field->getName();
			} else if ($field->hasOption('message')) {
				$message = $field->getName();
			}
		}
		
		if ($subject === null || $message === null) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('The fields subject and message '
				. 'options must be specified');
		}
		
		if (($headers = $builder->getOption('header', '')) !== '')  {
			$headers = implode("\r\n", (array) $headers);
		}
		
		mail($email, $subject, $message, $headers);
		return true;
	}
	
	public function delete(Atomik_Model $model)
	{
		return true;
	}
}