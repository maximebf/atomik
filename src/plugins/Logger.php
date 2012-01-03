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

class Logger
{
    /** @var array */
    public static $config = array();
    
    /**
     * Starts this class as a plugin
     *
     * @param array $config
     */
    public static function start(&$config)
    {
        $config = array_merge(array(
        
            /* @var bool */
            'register_default'   => false,
            
            /* @var string */
            'filename'           => 'log.txt',
            
            /* From which level to start logging messages
             * @var int */
            'level'              => LOG_WARNING,
            
            /* Message template for the default logger
             * @see logToFile()
             * @var string */
            'message_template'   => '[%date%] [%level%] %message%'
            
        ), $config);
        self::$config = &$config;
        
        Atomik::registerHelper('log', 'Atomik\Logger::log');
        if ($config['register_default']) {
            self::listenEvent('Logger::Log', 'Atomik\Logger::logToFile');
        }
    }
    
    /**
     * Fire an Atomik::Log event to which logger can listen
     * 
     * @param string $message
     * @param int $level
     */
    public static function log($message, $level = 3)
    {
        self::fireEvent('Logger::Log', array($message, $level));
    }
    
    /**
     * Default logger: log the message to the file defined in atomik/files/log
     * The message template can be define in atomik/log/message_template
     * 
     * @see Atomik::log()
     * @param string $message
     * @param int $level
     */
    public static function logToFile($message, $level)
    {
        if ($level > self::$config['level']) {
            return;
        }
        
        $filename = self::$config['filename'];
        $template = self::$config['message_template'];
        $tags = array(
            '%date%' => @date('Y-m-d H:i:s'), 
            '%level%' => $level,
            '%message%' => $message
        );
        
        $file = fopen($filename, 'a');
        fwrite($file, str_replace(array_keys($tags), array_values($tags), $template) . "\n");
        fclose($file);
        $file = null;
    }
}
