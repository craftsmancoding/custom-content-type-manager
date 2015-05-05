<?php
/**
* EDD Licence Class
* ACtivate And Check License key
**/
class CCTM_License {

	public static $store_url = 'http://craftsmancoding.com/products/'; // store_url
	public static $product_url = 'https://craftsmancoding.com/products/downloads/cctm/'; 
	public static $plugin = 'CCTM'; // item name from store
	public static $key_option_name = 'cctm_edd_license_key';
    public static $status_option_name = 'cctm_edd_license_status';

	public function __construct() 
	{	
		/*delete_option( 'cctm_edd_license_key' );
		delete_option( 'cctm_edd_license_status' );
		delete_transient(strtolower(str_replace(' ', '_', self::$plugin)) );
		die();*/
		$this->edd_register_option();
		$this->edd_activate_license();
	}

	/**
	* Activate License Page
	* Display License Filed and Activate button
	**/
	public static function activate_license_page() {
		$license 	= get_option( 'cctm_edd_license_key' );
		$status 	= get_option( 'cctm_edd_license_status' );

		?>
		<div class="wrap">
			<h2><?php _e('Plugin '.self::$plugin.' License Options'); ?></h2>
			<form method="post" action="options.php">
			
				<?php settings_fields('cctm_license'); ?>

				<table class="form-table">
					<tbody>
						<tr valign="top">	
							<th scope="row" valign="top">
								<?php _e('License Key'); ?>
							</th>
							<td>
								<input id="cctm_edd_license_key" name="cctm_edd_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
								<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">

							</td>
						</tr>
						<?php if( false !== $license ) { ?>
							<tr valign="top">	
								<th scope="row" valign="top">
									<?php _e('Activate License'); ?>
								</th>
								<td>
									<?php if( $status !== false && $status == 'valid' ) { ?>
										<span style="color:green;"><?php _e('active'); ?></span>
									<?php } else {
										wp_nonce_field( 'edd_nonce', 'edd_nonce' ); ?>
										<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License'); ?>"/>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			
			</form>
		<?php
	}

	public static function inactive_page() {
		?>
		<div id="cctm-warning" class="updated fade"><p><strong>CCTM is almost ready.</strong> You must <a href="admin.php?page=cctm_fields-activate">enter your License key</a> for it to work and show More CCTM Setting Pages.</p></div>
		<?php 
	}

	/**
	* edd_register_option
	**/
	public static function edd_register_option() {
		// creates our settings in the options table

		register_setting('cctm_license', 'cctm_edd_license_key', array('CCTM_License','edd_sanitize_license'));
	}

	
	/**
	* activate_license_menu
	* Add Plugin Licence Menu
	* This is a prepared function to add Custom Menu for the plugin
	* Usage: optional
	* They can add a custom menu as a sub page for Activate License
	**/
	public static function activate_license_menu() {
		add_plugins_page( 'Activate '.self::$plugin.' License', 'Activate ' .self::$plugin. ' License', 'administrator', 'activate-' .strtolower(str_replace(' ', '_', self::$plugin)). '-license', array('CCTM_License','activate_license_page') );
	}

	/**
	* edd_sanitize_license
	**/
	public static function edd_sanitize_license( $new ) {
		$old = get_option( 'cctm_edd_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'cctm_edd_license_status' ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	/**
	* edd_activate_license
	**/
	public static function edd_activate_license() {
		$license 	= get_option( 'cctm_edd_license_key' );

		$license = trim($license);


        update_option(self::$key_option_name, $license);
        
		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( self::$plugin ), // the name of our product in EDD,
			'url'       => home_url(),
			'rand' => uniqid().md5(home_url()) // cache-busting
		);
		
		// Call the custom API.
		$endpoint = add_query_arg( $api_params, self::$store_url );
		$response = wp_remote_get($endpoint);

		// make sure the response came back okay
		if (empty($response) || is_wp_error($response)) return false;
 		
		// decode the license data
		$license_data = json_decode(wp_remote_retrieve_body($response));
		

		if (empty($license_data) || !is_object($license_data)) return false;

		update_option(self::$status_option_name, $license_data->license);

		if($license_data->success) {
			return true;
		}
		return false;

	}

	/**
	* edd_check_license
	* cache the result using set_transient
	**/
	public static function edd_check_license() {	
		$license = trim( get_option( 'cctm_edd_license_key' ) );
		$status 	= get_option( 'cctm_edd_license_status' );
		$cache_key = strtolower(str_replace(' ', '_', self::$plugin));
		$data = get_transient( $cache_key );
		$key_old = trim( get_option( 'cctm_edd_license_key' ) );
	
		if ($data && $key_old == $data->key) {
			return $status;
		} else {
			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'check_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( self::$plugin ), // the name of our product in EDD,
				'url'       => home_url()
			);
		
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, self::$store_url ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;
			$data = json_decode( wp_remote_retrieve_body( $response ) );
			$data->key = trim( get_option( 'cctm_edd_license_key' ) );

	 		set_transient( $cache_key, $data, 60*60 );
			return $status;	
		}
				
	}

}

//register setting
add_action('admin_init', function(){
	new CCTM_License();
});