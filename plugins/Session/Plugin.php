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
 * Session plugin
 *
 * @package Atomik
 * @subpackage Plugins
 */
class SessionPlugin
{
    /**
     * Plugin initialization
     *
     * @param array $config
     * @return bool
     */
    public static function start($config)
    {
    	/** Atomik_Session */
        require_once 'Atomik/Session.php';
        
        /* starts the session */
        Atomik_Session::start();
        
        /* cleany close the session when atomik ends */
        Atomik::listenEvent('Atomik::End', array('Atomik_Session', 'end'));
        
        /* no needs to automatically register events */
        return false;
    }
}
