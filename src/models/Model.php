<?php
/**
 * 
 *
 * @package CCTM
 */
namespace CCTM\Models;

class Model extends \CCTM\Data {

    public static $Log;
    public static $Cache;
    public static $File;
    public static $Data;
    public static $get_option_function;
    public static $update_option_function;
    
    /**
     * Dependency injection used here to make this more testable.
     *
     * @param object $Log for logging info
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
        self::$Cache = $dependencies['Cache'];
        self::$File = $dependencies['File'];
//        self::$Data = $dependencies['Data'];
                
        self::$get_option_function = (isset($dependencies['get_option_function'])) 
            ? $dependencies['get_option_function'] 
            : function ($name,$default) { return $default; };
            
        self::$update_option_function = (isset($dependencies['update_option_function'])) 
            ? $dependencies['update_option_function'] 
            : function ($optionname,$value) { return true; };
    }
      
}