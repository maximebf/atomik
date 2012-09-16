<?php
/**
 * Atomik Framework
 * Copyright (c) 2008-2011 Maxime Bouroumeau-Fuseau
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Atomik
 * @author      Maxime Bouroumeau-Fuseau
 * @copyright   2008-2011 (c) Maxime Bouroumeau-Fuseau
 * @license     http://www.opensource.org/licenses/mit-license.php
 * @link        http://www.atomikframework.com
 */

namespace Atomik;

use Atomik;

class Assets
{
    public static $config = array();

    public static $loadedAssets = array();

    public static function start($config)
    {
        self::$config = array_merge(array(

            'packages' => array(),

            'assets_dir' => 'assets',

            'public_assets_dir' => 'assets',

            'allow_file_assets' => true,

            'css_filters' => array(),

            'css_extension' => 'css',

            'js_filters' => array(),

            'js_extension' => 'js'

        ), $config);

        Atomik::registerHelper('loadAsset', 'Atomik\Assets::load');
        Atomik::registerHelper('renderAssets', 'Atomik\Assets::render');

        if (Atomik::isPluginLoaded('Console')) {
            Console::register('write-assets', 'Atomik\Assets::write');
        }
    }

    public static function load($filename, $type = null)
    {
        if ($type === null) {
            if (strtolower(substr($filename, -4)) === '.css') {
                $type = 'css';
            } else {
                $type = 'js';
            }
        }
        self::$loadedAssets[$filename] = array($filename, $type);
    }

    public static function render($type = null)
    {
        $output = array();
        foreach (array_reverse(self::$loadedAssets) as $asset) {
            list($filename, $assetType) = $asset;
            if ($type !== null && $assetType !== $type) {
                continue;
            }
            $url = Atomik::asset(Atomik::path($filename, self::$config['public_assets_dir'], false, '/'));
            if ($type === 'css') {
                $output[] = sprintf('<link rel="stylesheet" type="text/css" href="%s" />', $url);
            } else if ($type === 'js') {
                $output[] = sprintf('<script type="text/javascript" src="%s"></script>', $url);
            }
        }
        $output = implode("\n", $output);
        Atomik::fireEvent('Assets::render', array(&$output, $type));
        return $output;
    }

    public static function write()
    {
        $publicDir = Atomik::path(self::$config['public_assets_dir'], Atomik::get('atomik/dirs/public'), false);

        Console::println("Writing packages to '$publicDir'");
        foreach (array_keys(self::$config['packages']) as $name) {
            $filename = Atomik::path($name, $publicDir, false);
            Console::touch($filename, self::dump($name), 1);
        }

        if (self::$config['allow_file_assets']) {
            $dir = Atomik::path(self::$config['assets_dir']);
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
            Console::println("Writing files to '$publicDir'");
            foreach ($it as $file) {
                if ($file->isDir() || substr($file->getFilename(), 0, 1) === '.') {
                    continue;
                }
                $filename = trim(substr($file->getPathname(), strlen($dir)), DIRECTORY_SEPARATOR);
                $pathname = Atomik::path(substr($filename, 0, strrpos($filename, '.')), $publicDir, false);
                if (in_array($file->getExtension(), (array) self::$config['css_extension'])) {
                    $pathname .= '.css';
                } else if (in_array($file->getExtension(), (array) self::$config['js_extension'])) {
                    $pathname .= '.js';
                } else {
                    $pathname .= '.' . $file->getExtension();
                }
                Console::touch($pathname, self::dump($filename), 1);
            }
        }

        Atomik::fireEvent('Assets::write');
    }

    public static function getPackageFiles($package)
    {
        $files = array();
        foreach ((array) self::$config['packages'][$package] as $file) {
            if ($file{0} === '@') {
                $files = array_merge($files, self::getPackageFiles(substr($file, 1)));
            } else {
                $files[] = $file;
            }
        }
        return $files;
    }

    public static function getFiltersForFile($filename)
    {
        $extension = strtolower(substr($filename, strrpos($filename, '.') + 1));
        $filters = array();
        if (in_array($extension, (array) self::$config['css_extension'])) {
            $filters = self::$config['css_filters'];
        } else if (in_array($extension, (array) self::$config['js_extension'])) {
            $filters = self::$config['js_filters'];
        }
        return array_map('Atomik\Assets::createFilter', $filters);
    }

    public static function createFilter($filter)
    {
        if (is_string($filter)) {
            $filter = new $filter();
        } else if (is_array($filter)) {
            $classname = array_shift($filter);
            $class = new \ReflectionClass($classname);
            $filter = $class->newInstanceArgs($filter);
        }
        return $filter;
    }

    public static function dump($filename)
    {
        $files = array();
        if (isset(self::$config['packages'][$filename])) {
            $files = self::getPackageFiles($filename);
        } else {
            $files = array($filename);
        }

        $assetsDir = Atomik::path(self::$config['assets_dir']);
        $assets = array();
        foreach ($files as $file) {
            $filters = self::getFiltersForFile($file);
            if ($file{0} === '!') {
                $file = substr($file, 1);
                $filters = array();
            }
            $className = 'Assetic\Asset\FileAsset';
            if (preg_match('/^[a-z]+:\/\//', $file)) {
                $className = 'Assetic\Asset\HttpAsset';
            } else {
                if (strpos($file, '*') !== false) {
                    $className = 'Assetic\Asset\GlobAsset';
                }
                $file = Atomik::path($file, $assetsDir, false);
            }
            $assets[] = new $className($file, $filters);
        }

        $collection = new \Assetic\Asset\AssetCollection($assets);
        Atomik::fireEvent('Assets::dump', array($collection));
        return $collection->dump();
    }

    public static function serve($filename)
    {
        $extension = strtolower(substr($filename, strrpos($filename, '.') + 1));
        $exists = isset(self::$config['packages'][$filename]);

        if (!$exists && self::$config['allow_file_assets']) {
            $pathname = Atomik::findFile($filename, Atomik::path(self::$config['assets_dir']));
            if ($pathname) {
                $exists = true;
            }
        }

        Atomik::fireEvent('Assets::serve', array(&$filename, &$exists));

        if (!$exists) {
            Atomik::trigger404();
        }
        if ($extension === 'css' || in_array($extension, (array) self::$config['css_extension'])) {
            header('Content-type: text/css');
        } else if ($extension === 'js' || in_array($extension, (array) self::$config['js_extension'])) {
            header('Content-type: text/javascript');
        }
        echo self::dump($filename);
    }

    public static function onAtomikDispatchUri($uri, $request, &$cancel)
    {
        $pattern = self::$config['public_assets_dir'] . '/*';
        if (!Atomik::uriMatch($pattern, $uri)) {
            return;
        }
        $uri = trim(substr(trim($uri, '/'), strlen(self::$config['public_assets_dir'])), '/');
        self::serve($uri);
        Atomik::end(true);
    }
}
