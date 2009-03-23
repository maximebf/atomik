<?php
/**
 * Atomik Framework
 * Copyright (c) 2008-2009 Maxime Bouroumeau-Fuseau
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
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * Lang plugin
 *
 * @package Atomik
 * @subpackage Plugins
 */
class LangPlugin
{
	/**
	 * Default configuration
	 * 
	 * @var array 
	 */
    public static $config = array(

    	/* default language */
    	'language'	    => 'en',
    	
    	/* autodetect browser language */
    	'autodetect'    => true,
    	
    	/* directory where language files are stored */
    	'dir' 		    => './app/languages/'
	
    );
    
    /**
     * Text messages
     *
     * @var array
     */
    protected static $_messages = array();
    
    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start($config)
    {
        /* config */
        self::$config = array_merge(self::$config, $config);
    }
    
    /**
     * Sets the language
     */
    public static function onAtomikStart()
    {
    	@session_start();
    	
    	/* language already discovered */
    	if (isset($_SESSION['__LANG']) && self::exists($_SESSION['__LANG'])) {
    		self::set($_SESSION['__LANG']);
    		return;
    	}
    	
    	/* autodetects language using HTTP_ACCEPT_LANGUAGE 
    	 * Language tag: primaryLang-subLang;q=? */
    	if (self::$config['autodetect'] === true) {
    		$acceptLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    		foreach ($acceptLanguages as $language) {
    			/* checks if sublang is supported */
    			$subLang = explode(';', $language);
    			if (self::exists($subLang[0])) {
    				self::set($subLang[0]);
    				return;
    			}
    			/* checks if primary lang is supported */
    			$primaryLang = explode('-', $subLang[0]);
    			if (self::exists($primaryLang[0])) {
    				self::set($primaryLang[0]);
    				return;
    			}
    		}
    	}
    	
    	/* uses default language */
    	if (self::exists($lang = self::$config['language'])) {
    		self::set($lang);
    	}
    }
    
    /**
     * Checks if a language is supported
     *
     * @param string $language
     * @param string|array $dirs OPTIONAL Directories where language files are stored
     * @return bool
     */
    public static function exists($language, $dirs = null)
    {
        if ($dirs === null) {
            $dirs = self::$config['dir'];
        }
        
    	/* filename of the language file */
    	$filename = Atomik::path($language . '.php', $dirs);
    	
    	/* checks if the file exists */
    	return file_exists($filename);
    }
    
    /**
     * Gets defined languages
     * 
     * @param string|array $dirs OPTIONAL Directories where language files are stored
     * @return array
     */
    public static function getDefinedLanguages($dirs = null)
    {
        if ($dirs === null) {
            $dirs = self::$config['dir'];
        }
        
        $languages = array();
        foreach (Atomik::path($dirs, true) as $dir) {
            if (is_dir($dir)) {
                foreach (new DirectoryIterator($dir) as $file) {
                    $filename = $file->getFilename();
                    if ($filename{0} == '.' || $file->isDir()) {
                        continue;
                    }
                    $languages[] = substr($filename, 0, strrpos($filename, '.'));
                }
            }
        }
        
        return $languages;
    }
    
    /**
     * Sets the language to use
     *
     * @param string $language OPTIONAL (default null) Null to use default language
     */
    public static function set($language = null)
    {
    	self::$_messages = array();
    	
    	/* uses the default language */
    	if ($language === null) {
    		$language = self::$config['language'];
    	}
    	
    	/* filename of the language file */
    	$filename = Atomik::path($language . '.php', self::$config['dir']);
    	
    	/* checks if the file exists */
    	if ($filename === false) {
    		throw new Exception('Language ' . $language . ' does not exists');
    	}
    	
    	/* include the language file */
    	include $filename;
    	
    	/* sets the current language */
    	Atomik::set('app/language', $language);
    	self::$config['language'] = $language;
    	$_SESSION['__LANG'] = $language;
    }
    
    /**
     * Gets the current language
     *
     * @return string
     */
    public static function get()
    {
    	return self::$config['language'];
    }
    
    /**
     * Sets messages
     * Must be used in language file
     *
     * @param array $messages
     */
    public static function setMessages($messages)
    {
    	self::$_messages = array_merge(self::$_messages, $messages);
    }
    
    /**
     * Translate a text. Works the same way as sprintf.
     *
     * @param string $text
     * @return string
     */
    public static function _($text)
    {
    	$args = func_get_args();
    	unset($args[0]);
    	
    	if (isset(self::$_messages[$text])) {
    		$text = self::$_messages[$text];
    	}
    	
    	$text = vsprintf($text, $args);
    	return $text;
    }
}

/* registers the __ functions if possible */
if (!function_exists('__')) {
    /**
     * Translate a text. Works the same way as sprintf.
     *
     * @see LangPlugin::_()
     * @param string $text
     * @return string
     */
    function __($text)
    {
    	$args = func_get_args();
    	return call_user_func_array(array('LangPlugin', '_'), $args);
    }
}
