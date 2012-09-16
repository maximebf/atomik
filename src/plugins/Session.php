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

class Session
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
        
            /* @var bool */
            'autostart' => true,

            /* @var string */
            'namespace' => false
            
        ), $config);
        self::$config = &$config;
    }
    
    public static function onAtomikStart()
    {
        if (self::$config['autostart']) {
            session_start();
            if (($ns = self::$config['namespace']) !== false) {
                if (!isset($_SESSION[$ns])) {
                    $_SESSION[$ns] = array();
                }
                Atomik::$store['session'] = &$_SESSION[$ns];
            } else {
                Atomik::$store['session'] = &$_SESSION;
            }
        }
    }
}
