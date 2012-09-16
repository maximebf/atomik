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

class DebugHelper
{
    /**
     * Equivalent to var_dump() but can be disabled using the configuration
     *
     * @see var_dump()
     * @param mixed $data The data which value should be dumped
     * @param bool $force Always display the dump even if debug from the config is set to false
     * @param bool $echo Whether to echo or return the result
     * @return string The result or null if $echo is set to true
     */
    public function debug($data, $force = false, $echo = true)
    {
        if (!$force && !Atomik::get('atomik/debug', false)) {
            return;
        }
        
        Atomik::fireEvent('Atomik::Debug', array(&$data, &$force, &$echo));
        
        // var_dump() does not support returns
        ob_start();
        var_dump($data);
        $dump = ob_get_clean();
        
        if (!$echo) {
            return $dump;
        }
        echo $dump;
    }
}
