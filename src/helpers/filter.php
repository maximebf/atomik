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

class FilterHelper
{
    /**
     * Filters data using PHP's filter extension
     * 
     * @see filter_var()
     * @param mixed $data
     * @param mixed $filter
     * @param mixed $options
     * @param bool $falseOnFail
     * @return mixed
     */
    function filter($data, $filter = null, $options = null, $falseOnFail = true)
    {
        if (is_array($data)) {
            // the $filter must be a rule or a string to a rule defined under app/filters/rules
            if (is_string($filter)) {
                if (($rule = Atomik::get('helpers/filters/rules/' . $filter, false)) === false) {
                    throw new AtomikException('When $data is an array, the filter must be an array of definition or a rule name in Atomik::filter()');
                }
            } else {
                $rule = $filter;
            }
            
            $results = array();
            $messages = array();
            $validate = true;
            
            foreach ($rule as $field => $params) {
                if (isset($data[$field]) && is_array($data[$field])) {
                    if (($results[$field] = $this->filter($data[$field], $params)) === false) {
                        $messages[$field] = Atomik::get('helpers/filters/messages', array());
                        $validate = false;
                    }
                    continue;
                }
                
                $filter = FILTER_SANITIZE_STRING;
                $message = Atomik::get('helpers/filters/default_message', 'The %s field failed to validate');
                $required = false;
                $default = null;
                $label = $field;
                if (is_array($params)) {
                    // extracting information from the array
                    if (isset($params['message'])) {
                        $message = Atomik::delete('message', $params);
                    }
                    if (isset($params['required'])) {
                        $required = Atomik::delete('required', $params);
                    }
                    if (isset($params['default'])) {
                        $default = Atomik::delete('default', $params);
                    }
                    if (isset($params['label'])) {
                        $label = Atomik::delete('label', $params);
                    }
                    if (isset($params['filter'])) {
                        $filter = Atomik::delete('filter', $params);
                    }
                    $options = count($params) == 0 ? null : $params;
                } else {
                    $filter = $params;
                    $options = null;
                }
                
                if (!isset($data[$field]) && !$required) {
                    // field not set and not required, do nothing
                    continue;
                }
                
                if ((!isset($data[$field]) || $data[$field] == '') && $required) {
                    // the field is required and either not set or empty, this is an error
                    $results[$field] = false;
                    $message = Atomik::get('helpers/filters/required_message', 'The %s field must be filled');
                    
                } else if ($data[$field] === '' && !$required) {
                    // empty but not required, null value
                    $results[$field] = $default;
                    
                } else {
                    // normal, validating
                    $results[$field] = $this->filter($data[$field], $filter, $options);
                }
                
                if ($results[$field] === false) {
                    // failed validation, adding the message
                    $messages[$field] = sprintf($message, $label);
                    $validate = false;
                }
            }
            
            Atomik::set('helpers/filters/messages', $messages);
            return $validate || !$falseOnFail ? $results : false;
        }
        
        if (is_string($filter)) {
            if (in_array($filter, filter_list())) {
                // filter name from the extension filters
                $filter = filter_id($filter);
                
            } else if (preg_match('@/.+/[a-zA-Z]*@', $filter)) {
                // regexp
                $options = array('options' => array('regexp' => $filter));
                $filter = FILTER_VALIDATE_REGEXP;
                
            } else if (($callback = Atomik::get('helpers/filters/callbacks/' . $filter, false)) !== false) {
                // callback defined under app/filters/callbacks
                $filter = FILTER_CALLBACK;
                $options = $callback;
            } 
        }
        
        return filter_var($data, $filter, $options);
    }
}
