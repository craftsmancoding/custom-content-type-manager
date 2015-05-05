<?php
/**
 * 
 * Formats a string into a date
 */
namespace CCTM\Filters;
class datef extends OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	string or an array of them
	 * @param	string date format	
	 * @return mixed
	 */
	public function filter($input, $options=null) {
        if (empty($input)) {
            return $input;
        }
		$format = get_option('date_format');

		if (!empty($options)) {
			$format = $options;
		}

		$inputs = $this->to_array($input);
		$output = '';

		if ($this->is_array_input) {
			foreach ($inputs as &$input) {
				$input = date($format, strtotime($input));
			}
			return $input;
		}
		else {
			return date($format, strtotime($inputs[0]));
		}
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __("The <em>datef</em> formats strings as dates.", CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return '<?php print_custom_field("'.$fieldname.':datef", "F j, Y, g:i a"); ?>';
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Date Format', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('https://code.google.com/p/wordpress-custom-content-type-manager/wiki/datef_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/