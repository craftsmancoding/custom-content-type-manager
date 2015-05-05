<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Implements PHPs htmspecialchars
 */

class CCTM_htmlspecialchars extends CCTM_OutputFilter {

	/**
	 * Replace any empty values with the $option.
	 *
	 * @param 	mixed 	input
	 * @param	string	options (none)
	 * @return mixed returns array if input was array, returns a string if input was string
	 */
	public function filter($input, $options=null) {
		$input = $this->to_array($input);
		if (!is_scalar($options)) {
			$options = '';
		}
		if (empty($input)) {
			if ($this->is_array_input) {
				return array($input);
			}
			else {
				return $input;
			}
		}
		else {
			if ($this->is_array_input) {
				foreach($input as &$item) {
					
						$item = $options;
					
				}
			}
			return $input;
		}
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>htmspecialchars</em> filter implements the PHP function of the same name.  This is useful if you need to print HTML data into form fields.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return "<input type=\"text\" value=\"<?php print_custom_field('$fieldname:htmspecialchars'); ?>\" />";
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('htmspecialchars', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/htmspecialchars_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/