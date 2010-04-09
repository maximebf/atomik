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

/**
 * @package Atomik
 * @subpackage Model
 */
interface Atomik_Model_EventListener
{
	function prepareQuery(Atomik_Model_Descriptor $descriptor, Atomik_Model_Query $query);
	function afterQuery(Atomik_Model_Descriptor $descriptor, $data);
	
	function beforeCreateInstance(Atomik_Model_Descriptor $descriptor, &$data);
	function afterCreateInstance(Atomik_Model_Descriptor $descriptor, Atomik_Model $model);
	
	function beforeSave(Atomik_Model_Descriptor $descriptor, Atomik_Model $model);
	function failSave(Atomik_Model_Descriptor $descriptor, Atomik_Model $model);
	function afterSave(Atomik_Model_Descriptor $descriptor, Atomik_Model $model);
	
	function beforeDelete(Atomik_Model_Descriptor $descriptor, Atomik_Model $model);
	function failDelete(Atomik_Model_Descriptor $descriptor, Atomik_Model $model);
	function afterDelete(Atomik_Model_Descriptor $descriptor, Atomik_Model $model);
	
	function beforeExport(Atomik_Model_Descriptor $descriptor, Atomik_Db_Schema $definition);
	function afterExport(Atomik_Model_Descriptor $descriptor, &$sql);
}