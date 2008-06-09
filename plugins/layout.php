<?php
	/**
	 * LAYOUT
	 *
	 * Adds layout support to templates
	 *
	 * @version 2.0
	 * @package Atomik
	 * @subpackage Layout
	 * @author Maxime Bouroumeau-Fuseau
	 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
	 * @license http://www.opensource.org/licenses/mit-license.php
	 * @link http://pimpmycode.fr/atomik
	 */
	 
	/* default configuration */
	config_set_default(array(
		'layout' => config_get('core_paths_templates') . '_layout.php'
	));

	/**
	 * Renders the layout
	 *
	 * @param string $content Template content
	 */
	function layout_render(&$content)
	{
		if (config_get('layout') !== false) {
			$content_for_layout = $content;
			
			/* renders layout */
			ob_start();
			include(config_get('layout'));
			$content = ob_get_clean();
			
			unset($content_for_layout);
		}
	}
	events_register('core_after_template', 'layout_render');

	/**
	 * Disables the layout
	 */
	function disable_layout()
	{
		config_set('layout', false);
	}

	/**
	 * Creates the layout file when the init command is called
	 * needs the console plugin
	 */
	function layout_console_init()
	{
		$filename = config_get('layout_filename');
		$layout = "<html>\n\t<head>\n\t\t<title>Atomik</title>\n\t</head>\n\t<body>\n\t\t"
				. "<?php echo \$content_for_layout; ?>\n\t</body>\n</html>";
		
		console_touch($filename, $layout, 1);
	}
	events_register('console_init', 'layout_console_init');

