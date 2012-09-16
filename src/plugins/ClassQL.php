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

class ClassQL
{
    public static function start($config)
    {
        $config = array_merge(array(

            'model_dirs' => array('Models' => 'models')

        ), $config);

        \ClassQL\Session::start($config);

        $loader = new \ClassQL\ModelLoader();
        $loader->add(array_filter((array) Atomik::path($config['model_dirs'])));
        $loader->register();

        if (Atomik::isPluginLoaded('Console')) {
            Console::register('classql', function($argv) {
                $cli = new \ClassQL\CLI();
                $cli->run($argv);
            });
        }
    }
}
