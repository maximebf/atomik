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
 * @subpackage Layout
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

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
	/**
	 * Default configuration
	 * 
	 * @var array 
	 */
	public static $config = array(

    	/* layout used troughout the site, false to disable */
    	'global' 	=> '_layout.php',
    	
    	/* layout used on a per template basis */
    	'templates'	=> array(),
	
	    /* whether layout are disabled or not */
	    'disable' 	=> false

    );
	
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
	 * Starts output buffering to capture the whole output
	 */
	public static function onAtomikDispatchBefore()
	{
		/* checks if global layout is enabled */
		if (self::$config['global'] === false) {
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
		if (($global = self::$config['global']) === false) {
			return;
		}
		
		$output = ob_get_clean();
		
		/* checks if the layout is enabled */
		if (self::$config['disable'] === false) {
			$output = Atomik::_renderInScope(
				Atomik::path($global, Atomik::get('atomik/dirs/templates')),
				array('content_for_layout' => $output)
			);
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
		$templates = self::$config['templates'];
		
		if (self::$config['disable'] === false && isset($templates[$template])) {
			$output = Atomik::_renderInScope(
				Atomik::path($templates[$template], Atomik::get('atomik/dirs/templates')),
				array('content_for_layout' => $output)
			);
		}
	}

	/**
	 * Disables the layout
	 */
	public static function disable($disable = true)
	{
		self::$config['disable'] = $disable;
	}

	/**
	 * Creates the layout file when the init command is called
	 * needs the console plugin
	 */
	public static function onConsoleInit()
	{
		ConsolePlugin::println('Generating layouts', 1);
		$templates = Atomik::get('atomik/dirs/templates');
		
		if (($global = self::$config['global']) !== false) {
		
    		$layout = "<html>\n\t<head>\n\t\t<title>Atomik</title>\n\t</head>\n\t<body>\n\t\t"
    				. "<?php echo \$content_for_layout; ?>\n\t</body>\n</html>";
    				
    		ConsolePlugin::touch(Atomik::path($templates) . $global, $layout, 2);
		}
			
		foreach (self::$config['templates'] as $filename) {
			$layout = "<?php echo \$content_for_layout; ?>";
			ConsolePlugin::touch(Atomik::path($templates) . $filename, $layout, 2);
		}
	}
}
