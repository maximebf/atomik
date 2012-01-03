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
use Atomik;

class HtmlHelper
{
    /**
     * Builds an html string from an array
     * 
     * Array's value can be a string or if the key is a string, which in this
     * case represent a tag name, an array.
     * Attributes start with @. Attributes can be defined as one array using
     * the "@" key (in this case, the attributes array keys do not need to start
     * with @).
     *
     * @param string|array $html
     * @param string $tag OPTIONAL Wrap the html into an element with this tag name
     * @param bool $dimensionize OPTIONAL Whether to use Atomik::_dimensionizeArray()
     * @return string
     */
    public function html($html, $tag = null, $dimensionize = false)
    {
        if ($tag !== null) {
            return $this->html(array($tag => $html));
        }
        
        if (is_string($html)) {
            return $html;
        }
        
        if ($dimensionize) {
            $html = Atomik::dimensionizeArray($html);
        }
        
        $output = '';
        foreach ($html as $tag => $value) {
            // do not parse attributes
            if (substr($tag, 0, 1) == '@') {
                continue;
            }
            // the key does not represent a tag
            if (!is_string($tag)) {
                if (!is_array($value)) {
                    $output .= $value;
                    continue;
                }
            }
            // the key is a tag
            $attributes = array();
            if (is_array($value)) {
                // has more than one children
                if (isset($value['@'])) {
                    // using the @ key for setting attributes
                    $attributes = $value['@'];
                } else {
                    // no @ key found, checking through all sub keys for the ones
                    // starting with @
                    foreach ($value as $attrName => $attrValue) {
                        if (substr($attrName, 0, 1) == '@') {
                            $attributes[substr($attrName, 1)] = $attrValue;
                        }
                    }
                }
                // parsing the value
                $keys = array_keys($value);
                if (!is_string($keys[0]) && is_array($value[0])) {
                    foreach ($value as $item) {
                        $output .= $this->html($item, $tag);
                    }
                    break;
                } else {
                    $value = $this->html($value);
                }
            }
            
            $output .= sprintf("<%s %s>%s</%s>\n", 
                $tag,
                Atomik::htmlAttributes($attributes), 
                $value, 
                $tag
            );
        }
        
        return $output;
    }
}
