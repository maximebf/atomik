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
abstract class Atomik_Model_Hydrator
{
    /** @var Atomik_Model_Descriptor */
    protected $_descriptor;
    
    /** @var Atomik_Db_Instance */
    protected $_db;
    
    /**
     * @param Atomik_Model_Descriptor $descriptor
     */
    public function __construct(Atomik_Model_Descriptor $descriptor)
    {
        $this->_descriptor = $descriptor;
        $this->_db = $descriptor->getDb();
    }
    
    /**
     * @return Atomik_Model_Descriptor
     */
    public function getDescriptor()
    {
        return $this->_descriptor;
    }
    
    /**
     * Returns a model hydrated using the data from
     * the database
     * 
     * @param array $data
     * @return Atomik_Model
     */
    abstract public function hydrate($data);
}