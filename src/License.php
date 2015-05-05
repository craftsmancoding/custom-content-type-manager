<?php
/**
 * Activate And Check License key
 */
namespace CCTM;
class License {

	public static $store_url = 'http://craftsmancoding.com/products/'; // store_url
    public static $product_url = 'http://craftsmancoding.com/products/downloads/custom-content-type-manager/'; 	
	public static $plugin = 'Custom Content Type Manager'; // item name from store
    public static $key_option_name = 'cctm_license_key';
    public static $status_option_name = 'cctm_license_status';
    

	/**
	 * Activate License Page
	 * Display License Filed and Activate button
	 */
	public static function activate_license_page() {
        $data 				= array();
        $data['license'] = get_option(self::$key_option_name);
        $data['status'] = get_option(self::$status_option_name);
        $data['page_title']	= __('License Options', CCTM_TXTDOMAIN);
        $data['menu'] 		= '';
        $data['msg']		= '';
        $data['content'] = Load::view('license.php',$data);
        print Load::view('templates/default.php', $data);
        return;
	}

    /** 
     * For admin notices
     */
	public static function get_error_msg() {
		?>
		<div id="cctm-warning" class="error fade"><p><strong>The CCTM is almost ready.</strong> You must <a href="admin.php?page=cctm_license">Enter a License Key</a>.  Licenses do not have a fixed price: they are "pay-what-you-want", including free!  So don't be shy.  <a href="http://craftsmancoding.com/products/downloads/custom-content-type-manager/" target="_blank">Get a License Key!</a>.</p></div>
		<?php 
	}

	/**
	 * register_option
	 */
	public static function register_option() {
		// creates our settings in the options table
		register_setting('cctm_license', self::$key_option_name, 'CCTM\License::sanitize');
	}

	
	/**
	 * Add Plugin License Menu
	 * This is a prepared function to add Custom Menu for the plugin
	 * Usage: optional
	 * They can add a custom menu as a sub page for Activate License
	 */
	public static function activate_license_menu() {
		add_plugins_page( 'Activate '.self::$plugin.' License', 'Activate ' .self::$plugin. ' License', 'administrator', 'activate-' .strtolower(str_replace(' ', '_', self::$plugin)). '-license', 'CCTM\License::activate_license_page');
	}

	/**
	 * Handle updated license keys
	 */
	public static function sanitize( $new ) {
		$old = get_option( self::$key_option_name );
		if( $old && $old != $new ) {
			delete_option( self::$status_option_name ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	/**
	 * activate
	 */
	public static function activate() {
 
		// listen for our activate button to be clicked
		if( isset( $_POST['edd_license_activate'] ) ) {

			// run a quick security check 
		 	if(!check_admin_referer( 'edd_nonce', 'edd_nonce')) return; 
		 		 
			// retrieve the license from the database
			$license = trim( get_option( self::$key_option_name ) );
				
			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'activate_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( self::$plugin ), // the name of our product in EDD,
				'url'       => home_url(),
				'rand' => uniqid().md5(home_url()) // cache-busting
			);
		
			// Call the custom API.
			$endpoint = add_query_arg($api_params, self::$store_url);

			$response = wp_remote_get($endpoint);
	 
			// make sure the response came back okay
			if (empty($response) || is_wp_error($response)) return false;
			// decode the license data
			$license_data = json_decode(wp_remote_retrieve_body($response));
			if(empty($license_data)) return false;
			update_option( self::$status_option_name, $license_data->license );	 
		}
	}

	/**
	 * check the license
	 * cache the result using set_transient
	 */
	public static function check() {	

		$license = trim( get_option( self::$key_option_name ) );
		$status 	= get_option( self::$status_option_name );
		$cache_key = strtolower(str_replace(' ', '_', self::$plugin));
		$data = get_transient( $cache_key );
		$key_old = trim( get_option( self::$key_option_name ) );
	
		if ($data && $key_old == $data->key) {
			return $status;
		} 
		else {
			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'check_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( self::$plugin ), // the name of our product in EDD,
				'url'       => home_url(),
				'rand' => uniqid().md5(home_url()) // cache-busting
			);
		
			// Call the custom API.
            $endpoint = add_query_arg( $api_params, self::$store_url);
			$response = wp_remote_get($endpoint);
	 
			// make sure the response came back okay
			if (empty($response) || is_wp_error($response)) return false;

			$data = json_decode( wp_remote_retrieve_body($response));
			if (empty($data)) return false;
			$data->key = trim( get_option( self::$key_option_name ) );
	 		set_transient( $cache_key, $data, 60*60 );
			return $status;	
		}			
	}
}