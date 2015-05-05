<?php
/**
 * Handles reading and creating Directories
 */
namespace CCTM;

class Dir {

    /** 
     * Append data to a file. $contents must be string. Will create the named 
     * $file if it does not exist. The directory must exist.
     *
     * @param string $file full filename including path
     * @param string $contents 
     * @return boolean true on success, false on fail.
     */
	public static function make($path) {
        if (!$path) return false;
        if (!is_scalar($path)) return false;        
        if (!file_exists($path)) return true;	
        if (!$fh = fopen($file, 'a')) return false;
        fwrite($fh, $contents);
        fclose($fh);
        return true;
    }
    
    /**
     * Read directory contents
     * @param string $path full directory pathname
     * @return array of directory contents
     */
	public static function read($path) {
        if (!$file) return false;
        if (!is_scalar($file)) return false;


    }
    
}