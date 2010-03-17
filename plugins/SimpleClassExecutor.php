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
 * @subpackage Plugins
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * @package Atomik
 * @subpackage Plugins
 */
class SimpleClassExecutorPlugin
{
    public static function start()
    {
        Atomik::set('app/executor', 'SimpleClassExecutorPlugin::execute');
    }
    
    /**
     * Executor which uses classes to define actions
     *
     * Searches for a file called after the action (with the php extension) inside
     * directories set under atomik/dirs/actions
     *
     * Each action file must have a class named after the action in camel case
     * and suffixed with "Action". If the action is in a sub directory, the class
     * name should follow the PEAR naming concention (ie. slashes => underscores).
     *
     * The class should have methods for each of the http method it wants to support.
     * The method should be lower cased. (eg: the GET method should be get() )
     *
     * The view variables are fetched from the return value of the method if its an 
     * array and using the class instance properties.
     *
     * @param string $action
     * @param string $method
     * @param array  $context
     * @return array
     */
    public static function execute($action, $method, $vars, $context)
    {
        if (($filename = Atomik::actionFilename($action)) === false) {
            throw new Atomik_Exception("Action file not found for $action", 404);
        }
        
        $className = str_replace(' ', '_', ucwords(str_replace('/', ' ', $action))) . 'Action';
        
        Atomik::fireEvent('SimpleClassExecutor::Execute', array(&$filename, &$className, &$context));
        
        require_once $filename;
        if (!class_exists($className)) {
            throw new Atomik_Exception("Class $className not found in $filename");
        }
        
        $instance = new $className($vars);
        $vars = array();
        
        if (method_exists($instance, 'execute')) {
            if (!is_array($vars = $instance->execute(Atomik::get('request')))) {
                $vars = array();
            }
        }
        
        if (method_exists($instance, $method)) {
            if (is_array($return = call_user_func(array($instance, $method), Atomik::get('request')))) {
                $vars = array_merge($vars, $return);
            }
        }
        
        $vars = array_merge(get_object_vars($instance), $vars);
        return $vars;
    }
}
