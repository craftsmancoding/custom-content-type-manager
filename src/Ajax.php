<?php
//------------------------------------------------------------------------------
/**
 * This class handles Ajax requests. The problem here is that WP requires that 
 * actions be hard-coded, and you can't add arguments to them.  So what we do
 * here is on instatiation, this class dynamically creates action-handlers 
 * for each controller file inside of the ajax-controllers directory, and 
 * this class does nonce-checking in order to make the ajax controllers 
 * as simplified as possible.
 *
 * This class is designed to be a pass-thru, so any Ajax request
 * gets routed here to the appropriate Ajax controller. Ajax calls should be
 * made like this:
 *
 * <script>
 * jQuery.post(
 * 		cctm.ajax_url,
 * 		{
 * 			action : 'my_ajax_controller',
 * 			my_ajax_controller_nonce : cctm.ajax_nonce
 * 			// additional variables here
 * 		},
 * 		function( response ) {
 * 			// ... do something ...
 * 		}
 * );
 * </script>
 *
 * @package CCTM
 */
namespace CCTM;
class Ajax {

	/**
	 * Contains key value pairs where key = basename of controller (no .php extension)
	 * and value is full path to the controller file.
	 */
	public static $controllers = array();

	//------------------------------------------------------------------------------
	/**
	 * Nonces exist in the $_POST array using the key named like this:
	 * conroller_name + _nonce.  The nonce is always named "ajax_nonce".
	 * WARNING: The response returned by the ajax-controllers *must* be wrapped in
	 * some kind of HTML tag, otherwise you can't use jQuery('#target_id').html(x)
	 * to write it.
	 *
	 * @param string $name of the method being called
	 * @param mixed $args sent to that method
	 */
	public static function __callStatic($name, $args) {
	
        $file = CCTM_PATH.'/ajax-controllers/'.$name.'.php';
		if (preg_match('/[a-z_\-]/',$name) || !file_exists($file)) {
			CCTM::log(sprintf(__('Invalid Ajax controller: %s', CCTM_TXTDOMAIN), "<em>$name</em>"),__FILE__,__LINE__);
			die(sprintf(__('Invalid Ajax controller: %s', CCTM_TXTDOMAIN), "<em>$name</em>"));
		}

		$nonce = CCTM::get_value($_REQUEST, $name.'_nonce');
		if ( ! wp_verify_nonce( $nonce, 'ajax_nonce' ) ) {
			CCTM::log(sprintf(__('Invalid nonce for %s', CCTM_TXTDOMAIN), "<em>$name</em>"),__FILE__,__LINE__);
			die(sprintf(__('Invalid nonce for %s', CCTM_TXTDOMAIN), "<em>$name</em>"));
		}

		include $file;

		exit; // terminate the request
	}


	//------------------------------------------------------------------------------
	/**
	 * The construct: here we add "listeners" to any defined Ajax event.  Each Ajax
	 * controller has its own event (i.e. action).
	 */
/*
	public function __construct() {
        // Scan directory
        $dir = CCTM_PATH .'/ajax-controllers';
        $rawfiles = scandir($dir);
        foreach ($rawfiles as $f) {
            if ( !preg_match('/^\./', $f) && preg_match('/\.php$/', $f) ) {
                $shortname = basename($f);
                $shortname = preg_replace('/\.php$/', '', $shortname);
                self::$controllers[$shortname] = $dir.'/'.$f;
            }
        }

        foreach (self::$controllers as $shortname => $path) {
            add_action( 'wp_ajax_'.$shortname, array($this, $shortname) );
        }
	}
*/
}

/*EOF*/