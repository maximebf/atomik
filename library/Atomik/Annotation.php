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
 * @subpackage Annotation
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

require_once dirname(__FILE__) . '/Annotation/addendum/annotations.php';

Addendum::ignore('author', 'package', 'subpackage', 'category', 'param', 'return', 'see');

/**
 * @package Atomik
 * @subpackage Annotation
 */
abstract class Atomik_Annotation extends Annotation
{
    public function getName()
    {
        $className = get_class($this);
        return substr($className, strrpos($className, '_') + 1);
    }
    
    public function toArray()
    {
        $class = new ReflectionClass($this);
        $array = array();
        
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $array[$prop->getName()] = $this->{$prop->getName()};
        }
        
        return $array;
    }
}