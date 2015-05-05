<?php
/**
 *
$value = Cache::rememberForever('users', function()
{
    return DB::table('users')->get();
}); 
 */
namespace CCTM\Controllers;
use CCTM;
class Customfields extends Controller {

    public static $Model;

    public function __construct(\Pimple $dependencies) {
        parent::__construct($dependencies);
        self::$Model = self::$Load->model('Customfield');
    }

    public function getIndex() {
        self::$View->fields = self::$Model->getAll();
        print self::$View->make('list_customfields.php');
    }
    
    public function getTypes() {
        self::$View->fields = self::$Model->getTypes();
        print self::$View->make('list_fieldtypes.php');        
    }
    
    public function getCreate($type) {
    
    }
    
    public function getDelete($name) {
    
    }
    
    public function postDelete($name) {
    
    }
    
    
}