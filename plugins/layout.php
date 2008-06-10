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
	
		/* layout used troughout the site, false to disable */
		'layout_global' 	=> config_get('core_paths_templates') . '_layout.php',
		
		/* layout used on a pair template basis */
		'layout_templates'	=> array()
		
	));

	/**
	 * Starts output buffering to capture the whole output
	 */
	function layout_before_dispatch()
	{
		/* checks if global layout is enabled */
		if (config_get('layout_global', false) === false) {
			return;
		}
		ob_start();
	}
	events_register('core_before_dispatch', 'layout_before_dispatch');
	 
	 /**
	  * Renders the global layout
	  */
	function layout_after_dispatch()
	{
		/* checks if global layout is enabled */
		if (config_get('layout_global', false) === false) {
			return;
		}
		$output = ob_get_clean();
		
		/* checks if the layout is enabled */
		if (config_get('layout', true) !== false) {
			$content_for_layout = $output;
			
			/* renders global layout */
			ob_start();
			include(config_get('layout_global'));
			$output = ob_get_clean();
		}
		
		echo $output;
	}
	events_register('core_after_dispatch', 'layout_after_dispatch');

	/**
	 * Renders the layout associated to a template
	 *
	 * @param string $template Template name
	 * @param string $output Template output
	 */
	function layout_render_template($template, &$output)
	{
		$templates = config_get('layout_templates');
		
		if (config_get('layout', true) !== false && isset($templates[$template])) {
			$content_for_layout = $output;
			
			/* renders layout */
			ob_start();
			include($templates[$template]);
			$output = ob_get_clean();
		}
	}
	events_register('core_after_template', 'layout_render_template');

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
		console_print('Generating layouts', 1);
		
		$layout = "<html>\n\t<head>\n\t\t<title>Atomik</title>\n\t</head>\n\t<body>\n\t\t"
				. "<?php echo \$content_for_layout; ?>\n\t</body>\n</html>";
		console_touch(config_get('layout_global'), $layout, 2);
		
		$layouts = config_get('layout_templates');
		foreach ($layouts as $filename) {
			$layout = "<?php echo \$content_for_layout; ?>";
			console_touch($filename, $layout, 2);
		}
	}
	events_register('console_init', 'layout_console_init');

