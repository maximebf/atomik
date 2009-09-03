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

/** Atomik_Model_Behaviour_Abstract */
require_once 'Atomik/Model/Behaviour/Abstract.php';

/** Atomik_Model_Field_Timestamp */
require_once 'Atomik/Model/Field/Timestamp.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Behaviour_Timestampable extends  Atomik_Model_Behaviour_Abstract
{
	public function init(Atomik_Model_Builder $builder)
	{
		if (!$builder->hasField('created')) {
			$builder->addField(new Atomik_Model_Field_Timestamp('created', array('form-ignore' => true)));
		}
		
		if (!$builder->hasField('updated')) {
			$builder->addField(new Atomik_Model_Field_Timestamp('updated', array('form-ignore' => true)));
		}
	}
	
	public function beforeSave(Atomik_Model_Builder $builder, Atomik_Model $model)
	{
		$now = date('Y-m-d H:i:s');
		if ($model->isNew()) {
			$model->created = $now;
		}
		$model->updated = $now;
	}
}