<?php

/**
 * Redirects to another url
 *
 * @see Atomik::url()
 * @param string $url The url to redirect to
 * @param bool $useUrl Whether to use Atomik::url() on $url before redirecting
 * @param int $httpCode The redirection HTTP code
 */
function redirect($url, $useUrl = true, $httpCode = 302)
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
