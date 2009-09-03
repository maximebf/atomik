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

/** Atomik_Form */
require_once 'Atomik/Form.php';

/** Atomik_Model */
require_once 'Atomik/Model.php';

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

/**
 * Creates a form for a model
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Form extends Atomik_Form
{
	/**
	 * @var Atomik_Model_Builder
	 */
	protected $_builder;
	
	/**
	 * @var Atomik_Model
	 */
	protected $_model;
	
	/**
	 * @var string
	 */
	protected $_optionPrefix;
	
	/**
	 * Constructor
	 *
	 * @param string|Atomik_Model_Builder|Atomik_Model $object
	 */
	public function __construct($object, $optionPrefix = 'form-')
	{
		$this->_optionPrefix = $optionPrefix;
		$this->setBuilder(Atomik_Model_Builder_Factory::get($object));
		if ($object instanceof Atomik_Model) {
			$this->setModel($object);
		}
		
		$this->setData(array_merge($_POST, $_FILES));
	}
	
	/**
	 * Sets the model builder associated to this form
	 * 
	 * @param Atomik_Model_Builder $builder
	 */
	public function setBuilder(Atomik_Model_Builder $builder)
	{
		$this->_builder = $builder;
		$this->_fields = array();
		
		$options = new Atomik_Options($builder->getOptions($this->_optionPrefix));
		$this->setFormTemplate($options->getOption('template', Atomik_Form::getDefaultFormTemplate()));
		$this->setFieldTemplate($options->getOption('field-template', Atomik_Form::getDefaultFieldTemplate()));
		$this->setOptions($options->getOptions());
		
		foreach ($builder->getFields() as $modelField) {
			if ($modelField->getOption('ignore', false, $this->_optionPrefix)) {
				continue;
			}
			
			$formField = null;
			$defaultLabel = $modelField->name;
			
			if ($builder->isFieldPartOfReference($modelField) && !$modelField->getOption('no-reference', false, $this->_optionPrefix)) {
				$reference = $builder->getReferenceFromSourceField($modelField);
				if ($reference->isHasMany()) {
					continue;
				}
				
				$defaultLabel = $reference->name;
				$options = $modelField->getOptions($this->_optionPrefix);
				$options['reference'] = $reference;
				
				require_once 'Atomik/Model/Form/Field/Reference.php';
				$formField = Atomik_Form_Field_Factory::factory('Atomik_Model_Form_Field_Reference', 
					$modelField->name, $options);
					
			} else {
				$formField = $modelField->getFormField();
			}
			
			$this->_fields[$modelField->name] = $formField;
			$this->_labels[$modelField->name] = $modelField->getLabel();
			$formField->setParent($this);
		}
	}
	
	/**
	 * Returns the model builder
	 * 
	 * @return Atomik_Model_Builder
	 */
	public function getBuilder()
	{
		return $this->_builder;
	}
	
	/**
	 * Adds a new field
	 * 
	 * @param	Atomik_Form_Field_Abstract	$field
	 * @param	string						$label
	 */
	public function addField(Atomik_Form_Field_Abstract $field, $label = null)
	{
		parent::addField($field, $label);
		$this->populateModel();
	}
	
	/**
	 * Sets the data
	 * 
	 * @param	array	$data
	 * @param	bool	$populateModel
	 */
	public function setData($data, $populateModel = true)
	{
		parent::setData($data);
		
		if ($populateModel) {
			$this->populateModel();
		}
	}
	
	/**
	 * Sets the model associated to this form
	 *
	 * @param Atomik_Model $model
	 */
	public function setModel(Atomik_Model $model, $updateData = true)
	{
		if ($model !== null && $model->getBuilder() !== $this->_builder) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Only models using builder ' . $this->_builder->name . ' can be used with the form');
		}
		
		$this->_model = $model;
		
		if ($model !== null && $updateData) {
			$this->setData($model->toArray(), false);
		}
	}
	
	/**
	 * Populates the model with the data from the form
	 */
	public function populateModel()
	{
		if ($this->_model === null) {
			$this->_model = $this->_builder->createInstance();
		}
		$this->_model->populate($this->getData());
	}
	
	/**
	 * Checks if a model is associated to this form
	 *
	 * @return bool
	 */
	public function hasModel()
	{
		return $this->_model !== null;
	}
	
	/**
	 * Returns the model associated to this form.
	 *
	 * @return Atomik_Model
	 */
	public function getModel()
	{
		return $this->_model;
	}
	
	/**
	 * Unsets the model associated to this form
	 */
	public function unsetModel()
	{
		$this->_model = null;
		$this->clearData();
	}
	
	/**
	 * Saves the current model
	 * 
	 * @see Atomik_Model::save()
	 * @return bool
	 */
	public function save()
	{
		return $this->_model->save();
	}
}