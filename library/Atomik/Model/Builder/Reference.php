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

/** Atomik_Db_Query */
require_once 'Atomik/Db/Query.php';

/**
 * References are used to describe relations between models. There is two
 * types of references: one and many. The first one points to one model and the
 * second one to more than one. The pointed model is called a foreign model.
 * 
 * The property used to access the reference can be set as a model name alias. (see $model)
 * If not set, the foreign model name will be used.
 * 
 * The condition that links two model can be defined using the $using parameter.
 * It can be an array containing a foreignField key which defined the field to use on the
 * foreign model and a localField key which defined the field to use on the local model.
 * The field's value must be equal for a relation to be established.
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Builder_Reference
{
	const HAS_ONE = 'one';
	
	const HAS_PARENT = 'parent';
	
	const HAS_MANY = 'many';
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $type = 'one';
	
	/**
	 * @var string
	 */
	public $sourceField;
	
	/**
	 * @var string
	 */
	public $target;
	
	/**
	 * @var string
	 */
	public $targetField;
	
	/**
	 * @var Atomik_Db_Query
	 */
	public $query;
	
	/**
	 * Constructor
	 * 
	 * @param	string	$type
	 */
	public function __construct($name, $type = 'one')
	{
		$this->name = $name;
		$this->type = $type;
		$this->query = new Atomik_Model_Query();
	}
	
	/**
	 * Checks if the type is "one"
	 * 
	 * @return bool
	 */
	public function isHasOne()
	{
		return $this->type == self::HAS_ONE;
	}
	
	/**
	 * Checks if the type is "parent"
	 * 
	 * @return bool
	 */
	public function isHasParent()
	{
		return $this->type == self::HAS_PARENT;
	}
	
	/**
	 * Checks if the type is "many"
	 * 
	 * @return bool
	 */
	public function isHasMany()
	{
		return $this->type == self::HAS_MANY;
	}
	
	/**
	 * Checks if the target is the specified model
	 * 
	 * @param	string|Atomik_Model_Builder|Atomik_Model 	$target
	 * @return 	bool
	 */
	public function isTarget($target)
	{
		if (is_string($target)) {
			return $target == $this->target;
		}
		if (!($target instanceof Atomik_Model_Builder)) {
			$target = Atomik_Model_Builder_Factory::get($target);
		}
		
		return $target->name == $this->target;
	}
	
	/**
	 * Returns the builder of the target
	 * 
	 * @return Atomik_Model_Builder
	 */
	public function getTargetBuilder()
	{
		return Atomik_Model_Builder_Factory::get($this->target);
	}
	
	/**
	 * Returns the query object to query the target model
	 * 
	 * @param	Atomik_Model	$sourceModel
	 * @return 	Atomik_Db_Query
	 */
	public function getQuery(Atomik_Model $sourceModel)
	{
		$query = clone $this->query;
		$query->from($this->getTargetBuilder())->where(array($this->targetField => $sourceModel->{$this->sourceField}));
		return $query;
	}
}