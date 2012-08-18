<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atomik;
use Atomik,
    AtomikException;

class File
{
    /**
     * Uploads a file
     * 
     * @param array $info The $_FILES entry
     * @param string $destFilename The destination filename
     * @param int $maxSize The maximum allowed file size
     * @param array $allowedExts Allowed extensions
     * @return bool Success
     */
    public static function upload($info, $destFilename, $maxSize = null, $allowedExts = null)
    {
        if ($info['error'] == UPLOAD_ERR_NO_FILE) {
            require_once 'Atomik/File/Exception.php';
        	throw new AtomikException('No file were uploaded');
        }
        
        if ($info['error'] == UPLOAD_ERR_INI_SIZE || 
            ($maxSize !== null && $info['size'] > $maxSize)) {
            require_once 'Atomik/File/Exception.php';
        	throw new AtomikException('File size is greater than allowed');
        }
        
        if ($info['error'] != UPLOAD_ERR_OK) {
            require_once 'Atomik/File/Exception.php';
        	throw new AtomikException('An error has occured while uploading');
        }
        
        $filename = basename($info['name']);
        $extension = '';
        if (strpos($filename, '.') !== false) {
            $extension = strtolower(substr($filename, strrpos($filename, '.') + 1));
        }
        
        if (is_array($allowedExts) && !in_array($extension, $allowedExts)) {
            require_once 'Atomik/File/Exception.php';
        	throw new AtomikException('File type not allowed');
        }
        
        return move_uploaded_file($info['tmp_name'], $destFilename);
    }
    
	/**
	 * Sends a file to the client
	 * 
	 * @param string $filename The file to send
	 * @param string $alias The name of the file as it will appear to the client
	 */
    public static function send($filename, $alias = null, $filesize = null)
    {
		if ($alias === null) {
			$alias = basename($filename);
		}
		
		if ($filesize === null) {
		    $filesize = filesize($filename);
		}
		
		self::sendHeaders($alias, $filesize);
		readfile($filename);
    }
    
    /**
     * Sends download headers
     * 
     * @param string $filename
     * @param string $filesize
     */
    public static function sendHeaders($filename, $filesize = null)
    {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		if ($filesize !== null) {
		    header('Content-Length: ' . $filesize);
		}
		ob_clean();
		flush();
    }
}
