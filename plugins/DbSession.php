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

Atomik::loadPlugin('Db');

/** Atomik_Session_Db */
require_once 'Atomik/Session/Db.php';

/**
 * @package Atomik
 * @subpackage Plugins
 */
class DbSessionPlugin
{
	/** @var array */
    public static $config = array(
        'instance' => 'default',
        'table' => 'sessions',
        'idColumn' => 'session_id',
        'dataColumn' => 'session_data',
        'expiresColumn' => 'session_expires'
    );
    
    /**
     * @param array $config
     */
    public static function start($config)
    {
        self::$config = array_merge(self::$config, $config);
        
        $db = Atomik_Db::getInstance(self::$config['instance']);
        $table = self::$config['table'];
        $idColumn =  self::$config['idColumn'];
        $dataColumn =  self::$config['dataColumn'];
        $expiresColumn =  self::$config['expiresColumn'];
        
        $handler = new Atomik_Session_Db($db, $table, $idColumn, $dataColumn, $expiresColumn);
        Atomik_Session_Db::register($handler);
    }
}