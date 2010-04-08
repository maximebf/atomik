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

/** Atomik_Model_Association */
require_once 'Atomik/Model/Association.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Association_OneToMany extends Atomik_Model_Association
{
    protected function _setup()
    {
		$this->_sourceField = $this->_source->getIdentifierField()->getName();
		$this->_targetField = $this->_source->getNameAsProperty() . ucfirst($this->_sourceField);
    }
    
    public function isMany()
    {
        return true;
    }
    
    public function load(Atomik_Model $model, $orderBy = null, $limit = null)
    {
        $data = $this->_createQuery($model, $orderBy, $limit)->executeData();
        $collection = new Atomik_Model_AssocCollection($model, $this, $data);
        $model->setProperty($this->_name, $collection);
    }
    
    public function save(Atomik_Model $model)
    {
        $coll = $model->getProperty($this->_name);
        $value = $model->getProperty($this->_sourceField);
        $changeset = $coll->getChangeset();
        
        foreach ($changeset['added'] as $target) {
            $target->setProperty($this->_targetField, $value);
            $target->save();
        }
        
        foreach ($changeset['removed'] as $target) {
            $target->setProperty($this->_targetField, null);
            $target->save();
        }
    }
}