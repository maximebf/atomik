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

namespace Atomik\Controller;

use Atomik,
    AtomikException,
    AtomikHttpException;

/**
 * Controller plugin
 * 
 * @package Atomik
 * @subpackage Plugins
 */
class Plugin
{
    /** @var array */
    public static $config = array();
    
    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start(&$config)
    {
        $config = array_merge(array(
        
            // default action name
            'default_action' => 'index',
        
            // directories where to find controllers
            'dirs' => 'controllers',

            // default controller namespaces
            'namespace' => '',
        
            // namespace separator
            'namespace_separator' => '\\'
            
        ), $config);
        
        self::$config = &$config;
        Atomik::set('app/executor', 'Atomik\Controller\Plugin::execute');
        Atomik::addIncludePath(array_filter(Atomik::path((array) self::$config['dirs'])));
    }
    
    /**
     * Executor which defines controllers and actions MVC-style
     *
     * @param string $action
     * @param string $method
     * @param array  $context
     * @return array
     */
    public static function execute($action, $method, $vars, &$context)
    {
        $controller = trim(dirname($action), './');
        $action = basename($action);
        if (empty($controller)) {
            $controller = $action;
            $action = self::$config['default_action'];
        }

        $className = trim(self::$config['namespace'] . self::$config['namespace_separator'] 
                   . str_replace(' ', self::$config['namespace_separator'], ucwords(str_replace('/', ' ', $controller))) 
                   . 'Controller', self::$config['namespace_separator']);
        
        Atomik::fireEvent('Controller::Execute', array(&$className));

        if (!class_exists($className)) {
            throw new AtomikHttpException("Class '$className' not found", 404);
        } else if (!is_subclass_of($className, 'Atomik\Controller\Controller')) {
            throw new AtomikException("Class '$className' must subclass 'Atomik_Controller'");
        }
        
        $instance = new $className();
        if (($instance->_dispatch($action, $method, $vars)) === false) {
            return false;
        }
        return get_object_vars($instance);
    }
}

