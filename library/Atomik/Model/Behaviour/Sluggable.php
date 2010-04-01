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

/**Atomik_Model_Behaviour */
require_once 'Atomik/Model/Behaviour.php';

/**
 * Inspired from the Doctrine behaviour of the same name
 *
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Behaviour_Sluggable extends Atomik_Model_Behaviour
{
    public $field = 'title';
    
	public function init(Atomik_Model_Descriptor $descriptor, $target)
	{
	    $descriptor->addField(Atomik_Model_Field::factory('slug', 'string', 100));
	}
	
	public function beforeSave(Atomik_Model_Descriptor $descriptor, Atomik_Model $model)
	{
    	if (!$descriptor->hasField($this->field)) {
    	    throw new Atomik_Model_Behaviour_Exception("Missing sluggable field '{$this->field}'");
    	}
		$model->setSlug(Atomik::friendlify($model->_get($this->field)));
	}
}
