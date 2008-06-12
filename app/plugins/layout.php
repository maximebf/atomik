<?php
/**
 * Atomik Framework
 * 
 * @package Atomik
 * @subpackage Layout
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/* default configuration */
Atomik::setDefault(array(
    'layout' => array(

    	/* layout used troughout the site, false to disable */
    	'global' 	=> '_layout.php',
    	
    	/* layout used on a pair template basis */
    	'templates'	=> array()

    )
));

/**
 * Layout plugin
 *
 * Adds layout support to templates
 *
 * @package Atomik
 * @subpackage Layout
 */
class LayoutPlugin
{
	protected static $_disable = false;
	
	/**
	 * Starts output buffering to capture the whole output
	 */
	public static function onAtomikDispatchBefore()
	{
		/* checks if global layout is enabled */
		if (Atomik::get('layout/global', false) === false) {
			return;
		}
		ob_start();
	}
	 
	 /**
	  * Renders the global layout
	  */
	public static function onAtomikDispatchAfter()
	{
		/* checks if global layout is enabled */
		if (($globalFilename = Atomik::get('layout/global', false)) === false) {
			return;
		}
		
		$output = ob_get_clean();
		
		/* checks if the layout is enabled */
		if (self::$_disable === false) {
			$content_for_layout = $output;
			
			/* renders global layout */
			ob_start();
			include(Atomik::get('atomik/paths/templates') . $globalFilename);
			$output = ob_get_clean();
		}
		
		echo $output;
	}

	/**
	 * Renders the layout associated to a template
	 *
	 * @param string $template Template name
	 * @param string $output Template output
	 */
	public static function onAtomikRenderAfter($template, &$output)
	{
		$templates = Atomik::get('layout/templates');
		
		if (self::$_disable === false && isset($templates[$template])) {
			$content_for_layout = $output;
			
			/* renders layout */
			ob_start();
			include(Atomik::get('atomik/paths/templates') . $templates[$template]);
			$output = ob_get_clean();
		}
	}

	/**
	 * Disables the layout
	 */
	public static function disable($disable = true)
	{
		self::$_disable = $disable;
	}

	/**
	 * Creates the layout file when the init command is called
	 * needs the console plugin
	 */
	public static function onConsoleInit()
	{
		ConsolePlugin::println('Generating layouts', 1);
		
		$layout = "<html>\n\t<head>\n\t\t<title>Atomik</title>\n\t</head>\n\t<body>\n\t\t"
				. "<?php echo \$content_for_layout; ?>\n\t</body>\n</html>";
				
		ConsolePlugin::touch(Atomik::get('layout/global'), $layout, 2);
		
		$layouts = Atomik::get('layout/templates');
		foreach ($layouts as $filename) {
			$layout = "<?php echo \$content_for_layout; ?>";
			ConsolePlugin::touch($filename, $layout, 2);
		}
	}
}
