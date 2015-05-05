<?php
/**
 * 
 *
 * @package CCTM
 */
namespace CCTM\Controllers;

class Controller {

    public static $Log;
    public static $View;
    public static $Load;
    public static $Cache;
    public static $Route;
    public static $POST;
    public static $GET;    
    /**
     * Dependency injection used here to make this more testable.
     *
     * @param object $Log for logging info
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
        self::$View = $dependencies['View'];
        self::$Load = $dependencies['Load'];
        self::$Cache = $dependencies['Cache'];
        self::$Route = $dependencies['Route'];
        self::$POST = $dependencies['POST'];
        self::$GET = $dependencies['GET'];        
    }  
    
    /**
     * Our 404
     *
     */
    public function __call($name,$args) {
        print View::make('error.php', array('msg'=>'Page not found: '.$name));
        self::$Log->debug('Page not found: '.$name, __CLASS__, __LINE__);
        return;
    }
}