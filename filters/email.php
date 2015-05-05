<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Obscures a string (e.g. an email address) to make it more difficult for it to 
 * be harvested by bots.
 */

class CCTM_email extends CCTM_OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	input
	 * @param	null	no options
	 * @return mixed matches input
	 */
	public function filter($input, $options=null) {
		$input = $this->to_array($input);
		if ($this->is_array_input) {
			foreach ($input as &$item) {
				$new_item = '';
				for ($i = 0; $i < strlen($item); $i++) { 
					$new_item .= '&#'.ord($item[$i]).';'; 
				}
				$item = $new_item;
			}
			// a raw array is more flexible than a canned filter...
			// return CCTM::filter($input, 'formatted_list', $options);
			return $input; 
		}
		else {
			$output = '';
			for ($i = 0; $i < strlen($input[0]); $i++) { 
				$output .= '&#'.ord($input[0][$i]).';'; // why is this an array? ord is weird.
			}
		}
		return $output;
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>email</em> filter obscures an email address so it is still readable by a human, but more difficult for it to be harvested by a spam-bot.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return "<?php print_custom_field('$fieldname:email'); ?>";
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Email', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/email_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/