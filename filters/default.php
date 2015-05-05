<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Returns a default value if the input value is empty.
 */

class CCTM_default extends CCTM_OutputFilter {

	/**
	 * Replace any empty values with the $option.
	 *
	 * @param 	mixed 	input
	 * @param	string	optional value to return if the input is empty
	 * @return mixed returns array if input was array, returns a string if input was string
	 */
	public function filter($input, $options=null) {
		$input = $this->to_array($input);
		if (!is_scalar($options)) {
			$options = '';
		}
		if (empty($input)) {
			if ($this->is_array_input) {
				return array($options);
			}
			else {
				return $options;
			}
		}
		else {
			if ($this->is_array_input) {
				foreach($input as &$item) {
					if (empty($item)) {
						$item = $options;
					}
				}
			}
			return $input;
		}
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>default</em> filter kicks in only if the input is empty: whatever you specify as an option will be returned only if the input is empty.  This is one way to establish default values for your fields.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return "<?php print_custom_field('$fieldname:default', '<em>unknown</em>'); ?>";
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Set Default Value', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/default_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/