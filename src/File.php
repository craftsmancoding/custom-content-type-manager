<?php
/**
 * Handles reading and writing of files
 */
namespace CCTM;

class File {

    /** 
     * Append data to a file. $contents must be string. Will create the named 
     * $file if it does not exist. The directory must exist.
     *
     * @param string $file full filename including path
     * @param string $contents 
     * @return boolean true on success, false on fail.
     */
	public static function append($file, $contents) {
        if (!$file) return false;
        if (!is_scalar($file)) return false;        
        if (!file_exists(dirname($file))) return false;	
        if (!$fh = fopen($file, 'a')) return false;
        fwrite($fh, $contents);
        fclose($fh);
        return true;
    }
    
    /**
     * Read data from a file
     * @param string $file full filename including path
     * @return mixed string of file contents on success, boolean false on fail
     */
	public static function read($file) {
        if (!$file) return false;
        if (!is_scalar($file)) return false;
        return file_get_contents($file);
    }
    
    /** 
     * Write string to a file.
     *
     * @param string $file full filename including path
     * @param mixed $contents string 
     * @return boolean true on success, false on fail.
     */
	public static function write($file, $contents) {
        if (!is_scalar($file)) return false;
        if (!file_exists(dirname($file))) return false;
        return file_put_contents($file, $contents);
    }
}