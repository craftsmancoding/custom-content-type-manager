<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Formats a string into a number format 
 */

class CCTM_number extends CCTM_OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	integer/string or an array of them
	 * @param	mixed	optional pipe-separated string containing the number of decimals (2), the thousands separator (,), the decimal separator (.). The default is 0|,|.
	 * @return mixed
	 */
	public function filter($input, $options=null) {
		$format = '0|,|.';
		$decimals = 0;
		$thousands_sep = ',';
		$dec_point = '.';

		if ($options == 0) {
			$decimals = 0;
		}
		elseif (!empty($options)) {
			$args = explode('|',$options);
		}

		$inputs = $this->to_array($input);
		$output = '';		
		
		if (isset($args[0])) {
			$decimals = $args[0];
		}
		if (isset($args[1])) {
			$thousands_sep = $args[1];
		}
		if (isset($args[2])) {
			$dec_point = $args[2];
		}

		if ($this->is_array_input) {
			foreach ($inputs as &$input) {
				$input = number_format($input, $decimals, $dec_point, $thousands_sep);
			}
			return $input;
		}
		else {
			return number_format($inputs[0], $decimals, $dec_point, $thousands_sep);
		}
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __("The <em>number</em> formats numbers in a readable way, e.g. for currency.", CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return '<?php print_custom_field("'.$fieldname.':number"); ?>';
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Money', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('https://code.google.com/p/wordpress-custom-content-type-manager/wiki/number_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/