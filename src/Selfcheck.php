<?php
/*------------------------------------------------------------------------------
Make sure all systems are go during plugin activation.  Run the "run"
function and every method name beginning with "test_" will be run.
The messages here are not localized: the libraries have not yet been loaded.
For unit tests, see the tests directory.

These are NOT written using a testing framework and do not require a specific 
database or theme.
------------------------------------------------------------------------------*/
namespace CCTM;

class Selfcheck {
    
	const wp_req_ver  = '3.3';
	const php_req_ver  = '5.3.0';
	const mysql_req_ver = '4.1.2';    
    
    // Names of any plugins incompatible with the CCTM
    
    public static $incompatible_plugins = array('Magic Fields','Custom Post Type UI','CMS Press');
    
    // Function names we delcare
    public static $function_names = array('get_custom_field','get_custom_field_meta'
    ,'get_custom_field_def','get_post_complete','get_posts_sharing_custom_field_value'
	,'get_relation','get_unique_values_this_custom_field','print_custom_field'
	,'print_custom_field_meta');

    // For any classes not in a namespace
    public static $class_names = array('CCTM','StandardizedCustomFields', 'SummarizePosts', 'GetPostsQuery', 'GetPostsForm','SP_Post');
	
    // Not class constants: constants declared via define():
    public static $constants = array('CCTM_PATH','CCTM_URL','CCTM_3P_PATH','CCTM_3P_URL','CCTM_TXTDOMAIN');

    // Used to store errors
    public static $errors = array();


    /**
     * Check for conflicting function names.
     */
    public static function _disabled_test_conflicting_functions() {
        foreach (self::$function_names as $f_name ) {
            if (function_exists($f_name)) {
            	self::$errors[] = sprintf('%1$s: %2$s', 'Conflicting Function', $f_name );
            }    
        }
    }
    
    /**
     * Check for conflicting Class names
     * This does not work with the auto-loader because class_exists triggers inclusion
     */
    public static function _disabled_test_conflicting_classnames() {
        foreach (self::$class_names as $cl_name ){
        	if (class_exists($cl_name)){
        		self::$errors[] = sprintf('%1$s: %2$s', 'Conflicting Class', $cl_name );
        	}
        }
    }
        
    /**
     * Check for conflicting Constants
     * This doesn't not work unless you conditionally define them (boo)
     */
    public static function _disabled_test_conflicting_constants() {
        foreach (self::$constants as $c_name ) {
        	if (defined($c_name) ) {
        		self::$errors[] = sprintf('%1$s: %2$s', 'Conflicting Constant', $c_name );
        	}
        } 
    }
    
	//------------------------------------------------------------------------------
	/**
	 * @param	array	list of names of incompatible plugins
	 * @return	null	CCTM::$warnings gets populated if errors are detected.
	 */
	public static function test_incompatible_plugins() {
		require_once ABSPATH.'/wp-admin/includes/admin.php';
		
		$all_plugins = get_plugins();
		$active_plugins = get_option('active_plugins');	

		$plugin_list = array();
		
		foreach (self::$incompatible_plugins as $bad_plugin ) {
			foreach ($active_plugins as $plugin) {
				if ( $all_plugins[$plugin]['Name'] == $bad_plugin ) {
					self::$errors[] = sprintf('Incompatible plugin: %s', $bad_plugin);
				}
			}
		}
		
	}
	
	//------------------------------------------------------------------------------
	 /**
	  * @param	string minimum req'd version of MySQL, e.g. 5.0.41
	  * @return none, but the $errors array is populated
	  */ 	
	public static function test_mysql_version_gt() {
		global $wpdb;
		
		$result = $wpdb->get_results( 'SELECT VERSION() as ver' );

		if ( version_compare( $result[0]->ver, self::mysql_req_ver, '<') ) {	
			self::$errors[] = sprintf('MySQL version %s or newer is required.', self::mysql_req_ver);
		}
	}
	
	//------------------------------------------------------------------------------
	public static function test_wp_version_gt() {
		global $wp_version;
		if (version_compare($wp_version,self::wp_req_ver,'<')) {
			self::$errors[] = sprintf('WordPress version %s or newer is required. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>', self::wp_req_ver );
		}
	}

	//------------------------------------------------------------------------------
	public static function test_php_version_gt() {		
		if ( version_compare( phpversion(), self::php_req_ver, '<') ) {
			self::$errors[] = sprintf( 'PHP version %s or newer is required.', self::php_req_ver );
		}
	}
	
	//------------------------------------------------------------------------------
	public static function test_reqd_classes() {
	   $classes = array(); // 
	   foreach ($classes as $c) {
    	   if (!class_exists($c)) {
    	       self::$errors[] = sprintf('The %s class is required.', $c);
    	   }
	   }
	}

	//------------------------------------------------------------------------------
	public static function test_reqd_functions() {
	   $functions = array('scandir'); // 
	   foreach ($functions as $f) {
    	   if (!function_exists($f)) {
    	       self::$errors[] = sprintf('The %s function is required.', $c);
    	   }
	   }
	}
	
	public static function test_upload_dir() {
        $upload_dir = wp_upload_dir();
		if (isset($upload_dir['error']) && !empty($upload_dir['error'])) {
			self::$errors[] = 'There was a problem with your upload directory: ' .$upload_dir['error'];
		}	
	}
	
	/**
	 * This only prints a message if there are errors.
	 * This should be the last function in the class due to how ReflectionClass
	 * iterates over the method names.
	 *
	 * This is NOT a static function so we can identify it via Reflection
	 */
	public static function print_errors() {
	   if (!empty(self::$errors)) {
	       $out = '
	       <style type="text/css">
        	.alert-box {
        		color:#555;
        		border-radius:10px;
        		font-family:Tahoma,Geneva,Arial,sans-serif;font-size:11px;
        		padding:10px 36px;
        		margin:10px;
        	}
        	.alert-box span {
        		font-weight:bold;
        		text-transform:uppercase;
        	}
        	.error {
        		background:#ffecec url("'.CCTM_URL.'/images/warning-icon.png") no-repeat 5px 50%;
        		border:1px solid #f5aca6;
        	}
        	</style>
	       <div class="alert-box error"><span>Error</span><br/>
	       The Custom Content Type Manager detected the following problems with your site that 
            prevented it from loading:<br/>
	           <ul>';
	       foreach (self::$errors as $e) {
	           $out .= '<li>'.$e.'</li>';
	       }
	       $out .= '</ul>
	       <p>See the wiki for more information on the <a href="https://code.google.com/p/wordpress-custom-content-type-manager/wiki/Requirements">CCTM installation requirements</a>.</p>
	       </div>';

	       print $out;
	       die();
	   }
	}
	
	/**
	 * Run every method beginning with the name "test_"
     *
	 */
	public static function run() {
	   $methods = get_class_methods(__CLASS__);
	   foreach ($methods as $m) {
	       if (strpos($m, 'test_') === 0) {
	           self::$m();
	       }
	   }
	   
	   self::print_errors();
	}
}
/*EOF*/