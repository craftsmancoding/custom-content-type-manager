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
class Settings extends Controller {

    public static $Model;

    public function __construct(\Pimple $dependencies) {
        parent::__construct($dependencies);
        self::$Model = self::$Load->model('Setting');
    }

    public function getIndex() {
        print self::$View->make('settings.php', self::$Model->getAll());
    }

    public function postIndex() {
//        self::$Model->fromArray()
        if (!self::$Model->save(self::$POST)) {
            print 'Error saving!'; exit;
        }
        self::$Route->reroute('settings/index', array('msg' => 'Settings updated.'));
//        print '<pre>'.print_r(self::$POST,true).'</pre>';
    }    
    
}