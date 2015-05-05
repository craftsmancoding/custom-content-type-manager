<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Takes an integer representing a user id and returns user data gleaned from the wp_users
 * and wp_usermeta table.  The password hash is intentionally omitted for security.
 * Note that get_user_by('id',$input) does NOT return all user metadata!!!
 */

class CCTM_userinfo extends CCTM_OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	user id or an array of them
	 * @param	mixed	optional formatting string
	 * @return mixed
	 */
	public function filter($input, $options=null) {
		$tpl = '<div class="cctm_userinfo" id="cctm_user_[+ID+]">[+user_nicename+]: [+user_email+]</div>';
		if (!empty($options)) {
			$tpl = $options;
		}

		$inputs = $this->to_array($input);
		$output = '';
		foreach ($inputs as $input) {
			$input = (int) $input;
			
			// Retrieve from CCTM request cache? (this caches data for a single request)
			// we cache the userdata array, keyed to a user ID
			if (isset(CCTM::$cache['userinfo'][$input])) {
				$output .= CCTM::parse($tpl, CCTM::$cache['userinfo'][$input]);
				continue;
			}
			
			global $wpdb;
			
			$userdata = array();
			$query = $wpdb->prepare("SELECT ID, user_login, user_nicename, user_email, 
				user_url, user_registered, user_activation_key, user_status, 
				display_name FROM ".$wpdb->prefix."users WHERE ID = %s", $input);
			$results = $wpdb->get_results($query, ARRAY_A);

			// No data for this user?
			if (!isset($results[0])) {
				CCTM::$cache['userinfo'][$input] = array(); // blank: prevents multiple queries
				continue;				
				// $output .= CCTM::parse($tpl, $userdata); // ??? should we???
			}
			
			// shift off the first (and only) result
			$userdata = $results[0]; 

			// Get metadata			
			$query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."usermeta WHERE user_id = %s", $input);
			$results = $wpdb->get_results($query, ARRAY_A);
			
			// No metadata (don't know how'd you get here, but just in case...)
			if (empty($results)) {
				CCTM::$cache['userinfo'][$input] = $userdata; 				
				$output .= CCTM::parse($tpl, $userdata);
				continue; // next user...
			}
			
			foreach ($results as $r) {
				$userdata[ $r['meta_key'] ] = $r['meta_value'];
			}
			
			CCTM::$cache['userinfo'][$input] = $userdata; 				
			$output .= CCTM::parse($tpl, $userdata);
		}
		
		return $output;
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __("The <em>userinfo</em> retrieves a user object by its user ID. It accepts an optional a formatting template to format the user information.", CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return '<?php print_custom_field("'.$fieldname.':userinfo"); ?>';
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('User', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/userinfo_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/