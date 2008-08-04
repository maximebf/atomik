<?php
/**
 * Atomik Framework
 * Copyright (c) 2008 Maxime Bouroumeau-Fuseau
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
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * Ajax plugin
 *
 * @package Atomik
 * @subpackage Plugins
 */
class AjaxPlugin
{
	/**
	 * Default configuration
	 * 
	 * @var array 
	 */
    public static $config = array(

    	/* disabe layouts for all templates when it's an ajax request */
    	'disable_layout'	=> true,
    
    	/* default action: restrict all (and use the ajax_allowed array) 
    	 * or allow all (and use the ajax_restricted array) */
    	'allow_all'	=> true,
    	
    	/* actions where ajax won't be available */
    	'restricted'	=> array(),
    	
    	/* actions where ajax will be available */
    	'allowed'		=> array()
    
    );
    
    /**
     * Returns false if it's not an AJAX request so
     * events are not automatically registered
     * 
     * @param array $config
     * @return bool
     */
    public static function start($config)
    {
        /* config */
        self::$config = array_merge(self::$config, $config);
        
        /* checks if this is an ajax request */
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        	return false;
        }
        
        return true;
    }
    
	/**
	 * Checks if it's needed to disable the layout
	 */
	public static function onAtomikStart()
	{
		if (self::$config['disable_layout']) {
			/* needs the layout plugin */
			if (Atomik::isPluginLoaded('layout')) {
				LayoutPlugin::disable();
			}
		}
	}

	/**
	 * After an allowed action has been executed, transform its $vars to
	 * a json string and echo it. Exists right after.
	 *
	 * @see Atomik::execute()
	 */
	public static function onAtomikExecuteAfter($action, &$template, &$vars, &$render, &$echo, &$triggerError)
	{
		if (!$echo) {
			return;
		}
		
		/* checks if ajax is enabled for this action */
		if (self::$config['allow_all']) {
			if (in_array($action, self::$config['restricted'])) {
				return;
			}
		} else {
			if (!in_array($action, self::$config['allowed'])) {
				return;
			}
		}
		
		self::endWithJson($vars);
	}
	
	/**
	 * Output vars as json and exits
	 *
	 * @param array $vars
	 */
	public static function endWithJson($vars = array())
	{
		/* builds the json string */
		$json = self::encode($vars);
		
		/* echo's output */
		header('Content-type: application/json');
		echo $json;
		
		/* ends successfuly */
		Atomik::end(true);
	}
	
	/**
	 * Encode a php array into a json string
	 * If the json_encode function is not found, uses a simple 
	 * built in encoder
	 * 
	 * @param array $array
	 * @return string
	 */
	public static function encode($array)
	{
        /* checks if the json_encode() function is available */
        if (function_exists('json_encode')) {
            return json_encode($array);
        }
        
		if (!is_array($array)) {
			if (is_string($array)) {
				return '"' . $array . '"';
			} else if (is_bool($array)) {
				return $array ? 'true' : 'false';
			} else {
				return $array;
			}
		}
		
		/* checks if it's an associative array */
		if (!empty($array) && (array_keys($array) !== range(0, count($array) - 1))) {
			$items = array();
			foreach ($array as $key => $value) {
				$items[] = "'$key': " . self::encode($value);
			}
			return '{' . implode(', ', $items) . '}';
		}
		
		/* standard array */
		$items = array();
		foreach ($array as $value) {
			$items[] = self::encode($value);
		}
		return '[' . implode(', ', $items) . ']';
	}
}

