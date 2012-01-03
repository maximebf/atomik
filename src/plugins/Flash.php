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

class Flash
{
    /**
     * Starts this class as a plugin
     */
    public static function start()
    {
        Atomik::registerHelper('flash', 'Atomik\Flash::flash');
        Atomik::registerHelper('flashMessages', 'Atomik\Flash::renderFlashMessages');
        Atomik::registerSelector('flash', 'Atomik\Flash::getFlashMessages');
    }
    
    /**
     * Saves a message that can be retrieve only once
     * 
     * @param string|array $message One message as a string or many messages as an array
     * @param string $label
     */
    public static function flash($message, $label = 'default')
    {
        if (!isset($_SESSION)) {
            throw new AtomikException('The session must be started before using Atomik::flash()');
        }
        
        Atomik::fireEvent('Atomik::Flash', array(&$message, &$label));
        
        if (!Atomik::has('session/__FLASH/' . $label)) {
            Atomik::set('session/__FLASH/' . $label, array());
        }
        Atomik::add('session/__FLASH/' . $label, $message);
    }
    
    /**
     * Returns the flash messages saved in the session
     * 
     * @internal 
     * @param string $label Whether to only retreives messages from this label. When null or 'all', returns all messages
     * @param bool $delete Whether to delete messages once retrieved
     * @return array An array of messages if the label is specified or an array of array message
     */
    public static function getFlashMessages($label = 'all', $delete = true) {
        if (!Atomik::has('session/__FLASH')) {
            return array();
        }
        
        if (empty($label) || $label == 'all') {
        	if ($delete) {
            	return Atomik::delete('session/__FLASH');
        	}
        	return Atomik::get('session/__FLASH');
        }
        
        if (!Atomik::has('session/__FLASH/' . $label)) {
            return array();
        }
        
        if ($delete) {
        	return Atomik::delete('session/__FLASH/' . $label);
        }
        return Atomik::get('session/__FLASH/' . $label);
    }
    
    /**
     * Renders the messages as html
     *
     * @param string $id The wrapping ul's id
     * @return string
     */
    public static function renderFlashMessages($id = 'flash-messages')
    {
        $html = '';
    	foreach (self::getFlashMessages() as $label => $messages) {
    	    foreach ($messages as $message) {
    	        $html .= sprintf('<li class="%s">%s</li>', $label, $message);
    	    }
    	}
    	if (empty($html)) {
    	    return '';
    	}
    	return '<ul id="' . $id . '">' . $html . '</ul>';
    }
}

