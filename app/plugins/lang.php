<?php
/**
 * Atomik Framework
 *
 * @package Atomik
 * @subpackage Lang
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/* default configuration */
Atomik::setDefault(array(
    'lang' => array(

    	/* default language */
    	'language'	    => 'en',
    	
    	/* autodetect browser language */
    	'autodetect'    => true,
    	
    	/* directory where language files are stored */
    	'dir' 		    => Atomik::get('atomik/paths/root') . 'languages/'
	
    )
));

/**
 * Lang plugin
 *
 * @package Atomik
 * @subpackage Lang
 */
class LangPlugin
{
    /**
     * Text messages
     *
     * @var array
     */
    protected static $_messages = array();
    
    /**
     * Sets the language
     */
    public static function onAtomikStart()
    {
    	@session_start();
    	
    	/* language already discovered */
    	if (isset($_SESSION['__LANG']) && self::exists($_SESSION['lang'])) {
    		self::set($_SESSION['__LANG']);
    		return;
    	}
    	
    	/* autodetects language using HTTP_ACCEPT_LANGUAGE 
    	 * Language tag: primaryLang-subLang;q=? */
    	if (Atomik::get('lang/autodetect') === true) {
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
    	if (self::exists($lang = Atomik::get('lang/language'))) {
    		self::set($lang);
    	}
    }
    
    /**
     * Checks if a language is supported
     *
     * @param string $language
     * @return bool
     */
    public static function exists($language)
    {
    	/* filename of the language file */
    	$dir = Atomik::get('lang/dir');
    	$filename = $dir . $language . '.php';
    	
    	/* checks if the file exists */
    	return file_exists($filename);
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
    		$language = Atomik::get('lang/language');
    	}
    	
    	/* filename of the language file */
    	$dir = Atomik::get('lang/dir');
    	$filename = $dir . $language . '.php';
    	
    	/* checks if the file exists */
    	if (!file_exists($filename)) {
    		throw new Exception('Language ' . $language . ' does not exists');
    	}
    	
    	/* include the language file */
    	include $filename;
    	
    	/* sets the current language */
    	Atomik::set('lang/language', $language);
    	$_SESSION['__LANG'] = $language;
    }
    
    /**
     * Gets the current language
     *
     * @return string
     */
    public static function get()
    {
    	return Atomik::get('lang/language');
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
        return LangPlugin::_($text);
    }
}
