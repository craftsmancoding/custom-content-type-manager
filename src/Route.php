<?php
/**
 * Maps a manager URL to a controller (and a few other tasks related to URLs, requests
 * and responses -- I'm bending the one-class one-purpose rule a bit)
 *
 * Wordpress uses only the "page" parameter to map a menu to a callback, and their
 * implementation is very simplified so that one menu item is bound to one action and 
 * to one permission.  So when we route URLs in the manager, we are actually mapping both
 * a request to a classname::function, but we are also "gating" it with a WP permission.
 * 
 * So the app here uses "virtual" urls to loosen things up a bit, even though the permissions
 * can only be locked down per controller class.  For convenience, the url() and a() functions
 * expect a shorthand similar to CodeIgniter: {$classname}/{$function}[/$optional/$args]
 *
 * This translates to GET parameters like this:
 * 
 *      &page=cctm_{$classname}  <-- must be defined via add_menu_page or add_submenu_page
 *      &route={$function[/$optional/$args]}
 *
 * Manager controllers are grouped into main classes.
 * URL segments can be used to map to the functions -- functions have "get" or "post" as
 * a prefix to designate whether or not they're handling a get or post request.
 *
 * The controller should eventually return a view.
 *
 * @package CCTM
 */
namespace CCTM;

class Route {
    
    public static $Log;
    public static $Load;
    public static $View;
    public static $POST;
    public static $GET;
    public static $create_nonce_function;
    public static $check_nonce_function;
    
    // We could inject these, but it'd prob'ly be overkill
    public static $slug_pre = 'cctm_'; 
    public static $class_param = 'page';
    public static $function_param = 'a';
    public static $route_param = 'route';
    public static $default_class = 'Posttypes';
    public static $default_function = 'index';
    public static $default_permission = 'cctm';
    public static $nonce_fieldname = '_cctmnonce';
    
    /**
     * Dependency injection used here to make this more testable.
     *
     * @param object $Log for logging info
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
        self::$Load = $dependencies['Load'];
        self::$View = $dependencies['View'];
        self::$POST = $dependencies['POST'];
        self::$GET = $dependencies['GET'];
        self::$create_nonce_function = (isset($dependencies['create_nonce_function'])) ? $dependencies['create_nonce_function'] : function ($name) { return ''; };
        self::$check_nonce_function = (isset($dependencies['check_nonce_function'])) ? $dependencies['check_nonce_function'] : function ($nonce,$name) { return true; };
        self::$Log->debug('Construct.', __CLASS__, __LINE__);
              
    }

    /**
     * Make a full anchor tag to a particular route
     *
     * @param string virtual $virtual_url
     * @param string clickable $title in the anchor tag
     * @param array optional $attributes to include in the link tag
     * @return string     
     */
    public static function a($virtual_url, $title='',$attributes=array()) {
        $url = self::url($virtual_url);
        return 'TEST';
    }

    /**
     * parse the request into classname + function + args, by 
     * listening for URL parameters
     *
     * @return array $class (str), $function (str), $args (array)
     */
    public static function parse() {
        $class = (isset(self::$GET[self::$class_param])) ? self::$GET[self::$class_param] : self::$default_class;
        // Strip prefix
        if (substr($class, 0, strlen(self::$slug_pre)) == self::$slug_pre) {
            $class = substr($class, strlen(self::$slug_pre));
        }
        $args = (isset(self::$GET[self::$route_param])) ? explode('/',self::$GET[self::$route_param]) : array();
        $function = ($args) ? array_shift($args) : self::$default_function;
        
        self::$Log->debug('Parsing Request -- Class: '.$class .' Function: '.$function .' Args: '.print_r($args,true), __CLASS__, __LINE__);        
        return array($class,$function,$args);
    }
    
    /**
     * Handle a request, triggering parsing of URL parameters...
     * could be in a dedicated "Request" class.
     */
    public static function handle() {
        list($class,$function,$args) = self::parse();
        return self::fulfill($class,$function,$args);
    }
    
    /**
     * Use reroute when you need to deflect one controller to another while
     * preserving some data, e.g. after a form submission fails validation
     * or use it to set "flash" messages without actually storing any data
     * in a session. 
     * 
     * This is a convenience function: you can also pass 4 arguments directly
     * to the "send" method.
     *
     * E.g. in a controller:
     *
     *  return self::$Route->reroute('customfields/types', array('msg' => 'Come here Instead'));
     *
     * @param $virtual_url, e.g. 'customfields/create'
     * @param $viewdata array any data to be passed to the view
     */
    public static function reroute($virtual_url, $viewdata=array()) {
        list($class,$function,$args) = self::split($virtual_url);
        return self::fulfill($class,$function,$args,$viewdata);
    }
    
    /** 
     *
     */
    public static function sendError($msg) {
        print View::make('error.php', array('msg'=>$msg));
        self::$Log->debug($msg, __CLASS__, __LINE__);
        return;    
    }
    
    /**
     * Validate and fulfill the request by handing off to a controller function.
     *
     * @param string $classname stub (namespace of CCTM\Controllers is assumed)
     * @param string $function i.e. method name
     * @param array $args any arguments to be passed to the controller (optional)
     * @param array $viewdata any data to be set on the view (optional)
     * @return string HTML web page usually
     */
    public static function fulfill($class,$function,array $args=array(),array $viewdata=array()) {
        // Our 404
        if(!$Controller = self::$Load->controller($class)) {
            return self::sendError('Controller class not found: '.$class);
        }
        foreach ($viewdata as $k => $v) {
            $Controller::$View->$k = $v;
        }
        $signature = $class.'/'.$function.'/'.implode('/',$args);

        if (self::$POST) {
            if (!isset(self::$POST[self::$nonce_fieldname])) {
                self::$Log->error('Post data missing nonce.', __CLASS__, __LINE__);
                return self::sendError('Post data missing nonce. ');
            }
            if (!call_user_func(self::$check_nonce_function, self::$POST[self::$nonce_fieldname], $signature)) {
                self::$Log->error('Invalid nonce for signature '.$signature, __CLASS__, __LINE__);
                return self::sendError('Invalid nonce.');
            }
            //unset(self::$POST[self::$nonce_fieldname]); //<-- fails because it's an overloaded object
            $function = 'post'.$function;
        }
        else {
            $nonce = call_user_func(self::$create_nonce_function, $signature);
            self::$Log->error('Generating nonce '.$nonce.' for signature '.$signature, __CLASS__, __LINE__);
            $Controller::$View->noncefield = '<input type="hidden" name="'.self::$nonce_fieldname.'" value="'.$nonce.'" />';
            $function = 'get'.$function;
        }
        self::$Log->debug('Sending request to '.$class .'::'.$function .' with args '.print_r($args,true).' viewdata: '.print_r($viewdata,true), __CLASS__, __LINE__);        
        return call_user_func_array(array($Controller, $function), $args);
    }

    /**
     * Translate a URL-ish string into 2 parts representing a controlloer and function.
     * e.g. 'some/thing' becomes array('some','thing)
     * whereas 'nada' becomes array('nada','index')
     *
     * The classname corresponds to WP's "menu slugs" which get passed in the "page"
     * parameter e.g. wp-admin/admin.php?page=cctm (see config/menu.php).
     *
     * @param string $str
     * @return array
     */
    private static function split($str) {
        $segments = explode('/',trim($str,'/'));
        $classname = ($segments) ? array_shift($segments) : self::$default_class;
        $function = ($segments) ? array_shift($segments) : self::$default_function;
        $args = ($segments) ? $segments : array();        
        return array($classname, $function, $args);
    }
        
    /**
     * The URL to a particular route in the format of $classname/$function[/$arg1/$arg2/...etc...]
     * The classname need not include the "cctm_" prefix that WordPress requires.
     *
     * @param string $virtual_url, e.g. 'customfields/edit/my-field'
     * @return string url, e.g. http://craftsmancoding.com/wp-admin/admin.php?page=cctm_customfields&route=edit/my-field
     */
    public static function url($virtual_url) {
        if (!is_scalar($virtual_url)) {
            self::$Log->error('$virtual_url must be a scalar: '.print_r($virtual_url,true), __CLASS__, __LINE__);
            return;
        }    
        list($class,$function,$args) = self::split($virtual_url);
        array_unshift($args,$function); // function is 1st argument
        $url = get_admin_url(false,'admin.php').'?page='.self::$slug_pre.$class.'&route='.implode('/',$args);
        self::$Log->debug('URL made from route '.$virtual_url. ' to '.$url, __CLASS__, __LINE__);
        return $url;
    }
}