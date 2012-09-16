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

class EscapeHelper
{
    /**
     * Escapes text so it can be outputted safely
     * 
     * Uses escape profiles defined in the escaping configuration key
     * 
     * @param string $text The text to escape
     * @param mixed $functions A profile name, a function name, or an array of function
     * @return string The escaped string
     */
    public function escape($text, $profile = array('htmlspecialchars', 'nl2br'))
    {
        if (!is_array($profile)) {
            if (($functions = Atomik::get('helpers/escape/' . $profile, false)) === false) {
                if (function_exists($profile)) {
                    $functions = array($profile);
                } else {
                    throw new AtomikException("No profile or functions named '$profile' in escape()");
                }
            }
        } else {
            $functions = $profile;
        }
        
        foreach ((array) $functions as $function) {
            $text = call_user_func($function, $text);
        }
        return $text;
    }
}

