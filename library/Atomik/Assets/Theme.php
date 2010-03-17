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
 * @subpackage Assets
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Assets */
require_once 'Atomik/Assets.php';

/**
 * @package Atomik
 * @subpackage Assets
 */
class Atomik_Assets_Theme extends Atomik_Assets
{
    /**
     * @var Atomik_Assets
     */
    protected $_manager;
    
    /**
     * @var string
     */
    protected $_themeDir;
    
    /**
     * @var string
     */
    private static $_themesDir = array();
	
	/**
	 * @var string
	 */
	private static $_defaultThemeBaseUrl;
	
	/**
	 * @param string $baseUrl
	 */
	public static function setDefaultThemeBaseUrl($baseUrl)
	{
	    self::$_defaultThemeBaseUrl = $baseUrl;
	}
	
	/**
	 * @return string
	 */
	public static function getDefaultThemeBaseUrl()
	{
	    if (self::$_defaultThemeBaseUrl === null) {
	        return Atomik_Assets::getDefaultBaseUrl();
	    }
	    return self::$_defaultThemeBaseUrl;
	}
    
	/**
	 * @param string $baseUrl
	 */
    public static function setThemesDir($dir)
    {
        self::$_themesDir = rtrim($dir, '/') . '/';
    }
	
	/**
	 * @return string
	 */
    public static function getThemesDir()
    {
        return self::$_themesDir;
    }
    
    /**
     * Creates a theme from the themes dir
     * 
     * @param string $name
     * @param Atomik_Assets $manager If not specified, will use {@see Atomik_Asset::getInstance()}
     * @return Atomik_Assets_Theme
     */
    public static function factory($name, $manager = null)
    {
        $dir = self::$_themesDir . $name;
        if (!is_dir($dir)) {
            throw new Atomik_Assets_Exception('Theme ' . $name . ' not found');
        }
        
        return new Atomik_Assets_Theme($dir, $manager);
    }
    
    /**
     * @param string $themeDir
     * @param Atomik_Assets $manager
     */
    public function __construct($themeDir = null, Atomik_Assets $manager = null)
    {
        parent::__construct();
        $this->_baseUrl = self::$_defaultThemeBaseUrl;
        
        if ($themeDir !== null) {
            $this->setThemeDir($themeDir);
        }
        
        if ($manager === null) {
            $manager = Atomik_Assets::getInstance();
        }
       $this->setManager($manager);
    }
    
    /**
     * @param string $themeDir
     */
    public function setThemeDir($themeDir)
    {
        $this->_themeDir = $themeDir;
        $this->setBaseUrl($themeDir);
    }
    
    /**
     * @return string
     */
    public function getThemeDir()
    {
        return $this->_themeDir;
    }
    
    /**
     * @param Atomik_Assets $manager
     */
    public function setManager(Atomik_Assets $manager)
    {
        $this->_manager = $manager;
    }
    
    /**
     * @return Atomik_Assets
     */
    public function getManager()
    {
        return $this->_manager;
    }
    
    /**
     * Computes themes assets according to managed assets
     */
    public function computeThemeAssets()
    {
        if ($this->_themeDir === null) {
            throw new Atomik_Assets_Exception('A theme directory must be specified');
        }
        
        if ($this->_manager === null) {
            throw new Atomik_Assets_Exception('Themes must be associated with a manager');
        }
        
        $assets = $this->_manager->getAssets();
        $dir = rtrim($this->_themeDir, '/') . '/';
        
        foreach ($assets as $asset) {
            if (file_exists($dir . ltrim($asset, '/'))) {
                $this->addAsset($asset);
            }
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see library/Atomik/Atomik_Assets#renderStyles()
     */
	public function renderStyles($computeThemeAssets = true)
	{
	    if ($computeThemeAssets) {
	        $this->computeThemeAssets();
	    }
	    return parent::renderStyles();
	}
    
	/**
	 * (non-PHPdoc)
	 * @see library/Atomik/Atomik_Assets#renderScripts()
	 */
	public function renderScripts($computeThemeAssets = true)
	{
	    if ($computeThemeAssets) {
	        $this->computeThemeAssets();
	    }
	    return parent::renderScripts();
	}
    
	/**
	 * (non-PHPdoc)
	 * @see library/Atomik/Atomik_Assets#render()
	 */
    public function render()
    {
	    $this->computeThemeAssets();
		return $this->renderStyles(false) . $this->renderScripts(false);
    }
}