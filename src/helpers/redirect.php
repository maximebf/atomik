<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atomik\Helpers;

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
