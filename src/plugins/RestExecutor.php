<?php
/**
 * Atomik Framework
 * Copyright (c) 2008-2011 Maxime Bouroumeau-Fuseau
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Atomik
 * @author      Maxime Bouroumeau-Fuseau
 * @copyright   2008-2011 (c) Maxime Bouroumeau-Fuseau
 * @license     http://www.opensource.org/licenses/mit-license.php
 * @link        http://www.atomikframework.com
 */

namespace Atomik;
use Atomik,
    AtomikException;

class RestExecutor
{
    /** @var array */
    public static $config = array();
    
    /**
     * Starts this class as a plugin
     *
     * @param array $config
     */
    public static function start(&$config)
    {
        $config = array_merge(array(

            /* @var string */
            'namespace_separator' => '\\'
            
        ), $config);
       
        Atomik::set('app/executor', 'Atomik\RestExecutor::execute');
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
        $className = str_replace(' ', self::$config['namespace_separator'], ucwords(str_replace('/', ' ', trim($action))));
        $filename = str_replace(self::$config['namespace_separator'], DIRECTORY_SEPARATOR, $className) . '.php';
        $className .= 'Action';
        
        Atomik::fireEvent('RestExecutor::Execute', array(&$className, &$filename, &$context));

        if (!($include = Atomik::actionFilename($filename, null, true))) {
            return false;
        }

        list($filename, $ns) = $include;
        $className = trim("$ns\\$className", '\\');
        include $filename;
        
        if (!class_exists($className)) {
            throw new AtomikException("Class '$className' not found in '$filename'");
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
