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

/** Atomik_Assets */
require_once 'Atomik/Assets.php';

/**
 * @package Atomik
 * @subpackage Plugins
 */
class AssetsPlugin
{
	/**
	 * Default configuration
	 * 
	 * @var array 
	 */
    public static $config = array (
        
        // @see Atomik_Assets::setDefaultBaseUrl()
        'assets_base_url' => 'assets',
    
        // the assets file
        'assets_file' => './app/assets.php',
    
        // @see Atomik_Assets_Theme::setDefaultThemesDir()
        'themes_dir' => 'assets/themes',
    
        // @see Atomik_Assets_Theme::setDefaultThemeBaseUrl()
        'theme_base_url' => 'assets/themes',
    
        // theme name
        'theme' => 'default',
    
        // whether to treat named assets starting with @
        // as resources
        'allow_resource_assets' => true
    
    );
    
    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start($config)
    {
    	self::$config = array_merge(self::$config, $config);
		Atomik::add('atomik/dirs/helpers', dirname(__FILE__) . '/helpers');
		
		Atomik_Assets::setDefaultUrlFormater('Atomik::asset');
		Atomik_Assets::setDefaultBaseUrl(self::$config['assets_base_url']);
		
		Atomik_Assets_Theme::setThemesDir(self::$config['themes_dir']);
		Atomik_Assets_Theme::setDefaultThemeBaseUrl(self::$config['theme_base_url']);
    	
    	if (file_exists(self::$config['assets_file'])) {
    	    self::loadAssetsFile(self::$config['assets_file']);
    	}
    }
    
    /**
     * Loads an assets file
     * 
     * An assets file is a php file where the $assets variable will be available
     * and points to an instance of Atomik_Assets
     * 
     * @param string $filename
     */
    public static function loadAssetsFile($filename)
    {
        $assets = Atomik_Assets::getInstance();
        include $filename;
    }
    
    /**
     * @return Atomik_Assets_Theme
     */
    public static function getTheme()
    {
        return Atomik_Assets_Theme::factory(self::$config['theme']);
    }
    
    public static function onAtomikDispatchBefore()
    {
        if (!self::$config['allow_resource_assets']) {
            return;
        }
        
        $assets = Atomik_Assets::getInstance();
        $namedAssets = array_keys($assets->getRegisteredNamedAssets());
        foreach ($namedAssets as $namedAsset) {
            if ($namedAsset{0} === '@' && Atomik::uriMatch(substr($namedAsset, 1))) {
                $assets->addNamedAsset($namedAsset);
                return;
            }
        }
    }
}

