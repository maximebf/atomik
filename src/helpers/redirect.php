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

class RedirectHelper
{
    /**
     * Redirects to another url
     *
     * @see Atomik::url()
     * @param string $url The url to redirect to
     * @param bool $useUrl Whether to use Atomik::url() on $url before redirecting
     * @param int $httpCode The redirection HTTP code
     */
    public function redirect($url, $useUrl = true, $httpCode = 302)
    {
        Atomik::fireEvent('Atomik::Redirect', array(&$url, &$useUrl, &$httpCode));
        if ($url === false) {
            return;
        }
        
        if ($useUrl) {
            $url = Atomik::url($url);
        }
        
        if (isset($_SESSION)) {
            $session = $_SESSION;
            // seems to prevent a php bug with session before redirections
            session_regenerate_id(true);
            $_SESSION = $session;
            // avoid loosing the session
            session_write_close();
        }
        
        header('Location: ' . $url, true, $httpCode);
        Atomik::end(true, false);
    }
}
