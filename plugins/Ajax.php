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
    	'allow_all'			=> false,
    	
    	/* actions where ajax won't be available */
    	'restricted'		=> array(),
    	
    	/* actions where ajax will be available */
    	'allowed'			=> array()
    
    );
    
    protected static $enabled = false;
    
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
        
        self::$enabled = true;
        return true;
    }
    
	/**
	 * Checks if it's needed to disable the layout
	 */
	public static function onAtomikStart()
	{
		if (self::$config['disable_layout']) {
			Atomik::disableLayout();
		}
	}

	/**
	 * After an allowed action has been executed, transform its $vars to
	 * a json string and echo it. Exits right after.
	 *
	 * @see Atomik::execute()
	 */
	public static function onAtomikExecuteAfter($action, &$context, &$vars, &$triggerError)
	{
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
		$json = json_encode($vars);
		
		/* echo's output */
		header('Content-type: application/json');
		echo $json;
		
		/* ends successfuly */
		Atomik::end(true);
	}
	
	/**
	 * Checks if the current request has been made with AJAX
	 * 
	 * @return bool
	 */
	public static function isEnabled()
	{
		return self::$enabled;
	}
}

