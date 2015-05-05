<?php
/**
 * The View object stores data which is accessed directly by the view file
 * The view class leverages a separate "Data" class used for storage so we can
 * easily pass the data to the view file.
 *
 * @package CCTM
 */
namespace CCTM;

class View {
    
    public static $Log;
    public static $Data;
    public static $POST;
    public static $GET;

    public static $path;    
    /**
     * @object Pimple dependency container
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
        self::$Data = $dependencies['Data'];
        self::$POST = $dependencies['POST'];
        self::$GET = $dependencies['GET'];
        
        // Some defaults for our views
        self::$Data->help = 'https://code.google.com/p/wordpress-custom-content-type-manager/';
    }
    
    /**
     * Our Getter
     */
    public function __get($key) {
        return (isset(self::$Data->$key)) ? self::$Data->$key : null;
    }
    
    /**
     * Our setter
     */
    public function __set($key,$value) {
        self::$Data->$key = $value;
    }

    /**
     * Where are our view fields stored?
     */
    public static function getPath() {
        return (self::$path) ? self::$path : CCTM_PATH.'/views/'; 
    }
    
    /**
     * You may override the path for testing or for third-party components.
     */
    public static function setPath($path) {
        self::$path = $path;
    }

    public static function is_checked($val) {
        return ($val) ? ' checked="checked"' : '';
    }
	/**
	 * Load up a PHP file into a string via an include statement. MVC type usage here.
	 *
	 * @param string  $filename (relative to the "views/" directory)
	 * @param array  $args. Instead of setting attributes on the View object, you can 
	 *     pass them here. Useful for simple views or for static invocation.
	 * @param boolean $debug (optional) set this if you want to see what your views are doing
	 * @return string the parsed contents of that file
	 */    
	public static function make($filename, array $args=array(),$debug=false) {
        // Last minute setting
        foreach ($args as $k => $v) {
            self::$Data->$k = $v;
        }

        $path = self::getPath();
        self::$Log->debug('View parameters -- filename: ' .print_r($filename,true). ' data: '.print_r((array)self::$Data,true). ' path: '.$path, __CLASS__,__LINE__);

		$data =& self::$Data;
		if (is_file($path.$filename)) {
			ob_start();
			include $path.$filename;
			if ($debug) {
    			ob_get_clean();		
    			return print_r((array) self::$Data->notset);                
			}
			return ob_get_clean();
		}
		self::$Log->error('View file does not exist: ' .$path.$filename, __CLASS__,__LINE__);
		return 'View file does not exist: ' .$path.$filename;
	}
}