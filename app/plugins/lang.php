<?php
	/**
	 * LANG
	 *
	 * @version 1.0
	 * @package Atomik
	 * @subpackage Lang
	 * @author Maxime Bouroumeau-Fuseau
	 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
	 * @license http://www.opensource.org/licenses/mit-license.php
	 * @link http://pimpmycode.fr/atomik
	 */

	/* default configuration */
	config_set_default(array(
	
		/* default language */
		'language'				=> 'en',
		
		/* autodetect browser language */
		'language_autodetect' 	=> true,
		
		/* directory where language files are stored */
		'language_dir' 			=> config_get('core_paths_root') . 'languages/'
		
	));
	
	/**
	 * Sets the language
	 */
	function lang_core_start()
	{
		@session_start();
		
		/* language already discovered */
		if (isset($_SESSION['__LANG']) && language_exists($_SESSION['lang'])) {
			set_language($_SESSION['__LANG']);
			return;
		}
		
		/* autodetects language using HTTP_ACCEPT_LANGUAGE 
		 * Language tag: primaryLang-subLang;q=? */
		if (config_get('language_autodetect') === true) {
			$acceptLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			foreach ($acceptLanguages as $language) {
				/* checks if sublang is supported */
				$subLang = explode(';', $language);
				if (language_exists($subLang[0])) {
					set_language($subLang[0]);
					return;
				}
				/* checks if primary lang is supported */
				$primaryLang = explode('-', $subLang[0]);
				if (language_exists($primaryLang[0])) {
					set_language($primaryLang[0]);
					return;
				}
			}
		}
		
		/* uses default language */
		if (language_exists(config_get('language'))) {
			set_language(config_get('language'));
		}
	}
	events_register('core_start', 'lang_core_start');
	
	/**
	 * Checks if a language is supported
	 *
	 * @param string $language
	 * @return bool
	 */
	function language_exists($language)
	{
		/* filename of the language file */
		$dir = config_get('language_dir');
		$filename = $dir . $language . '.php';
		
		/* checks if the file exists */
		return file_exists($filename);
	}
	
	/**
	 * Sets the language to use
	 *
	 * @param string $language OPTIONAL (default null) Null to use default language
	 * @return bool Success
	 */
	function set_language($language = null)
	{
		global $_ATOMIK;
		$_ATOMIK['language'] = array();
		
		/* uses the default language */
		if ($language === null) {
			$language = config_get('language');
		}
		
		/* filename of the language file */
		$dir = config_get('language_dir');
		$filename = $dir . $language . '.php';
		
		/* checks if the file exists */
		if (!file_exists($filename)) {
			trigger_error('Language ' . $language . ' does not exists', E_USER_WARNING);
			return false;
		}
		
		/* include the language file */
		include $filename;
		
		/* sets the current language */
		config_set('language', $language);
		$_SESSION['__LANG'] = $language;
		
		return true;
	}
	
	/**
	 * Gets the current language
	 *
	 * @return string
	 */
	function get_language()
	{
		return config_get('language');
	}
	
	/**
	 * Sets messages
	 * Must be used in language file
	 *
	 * @param array $messages
	 */
	function set_language_messages($messages)
	{
		global $_ATOMIK;
		$_ATOMIK['language'] = array_merge($_ATOMIK['language'], $messages);
	}

	/**
	 * Translate a text. Works the same way as sprintf.
	 *
	 * @param string $text
	 * @return string
	 */
	function __($text)
	{
		global $_ATOMIK;
		
		$args = func_get_args();
		unset($args[0]);
		
		if (isset($_ATOMIK['language'][$text])) {
			$text = $_ATOMIK['language'][$text];
		}
		
		$text = vsprintf($text, $args);
		return $text;
	}
	
