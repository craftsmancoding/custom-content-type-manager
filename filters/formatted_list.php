<?php
/**
 * @package CCTM_OutputFilter
 *
 * Filter takes an array (JSON or PHP) and formats it into a string.   Arrays can be
 * simple lists, or associative key/value pairs.
 *
 * @package CCTM_formatted_list
 */


class CCTM_formatted_list extends CCTM_OutputFilter {

	/**
	 * Checks whether a given array is a regular array or an associative array.
	 *
	 * @return boolean true if associative
	 * @param array   $array either a regular or an associative array.
	 */
	private function _is_assoc($array) {
		if (is_array($array)) {
			return false;
		}
		
		foreach ($array as $k => $v) {
			if ($k == 0) {
				return false;
			}
			else {
				return true;
			}
		}
	}

	//------------------------------------------------------------------------------
	//! Public functions
	//------------------------------------------------------------------------------
	/**
	 * Format an array of values.
	 *
	 * @param mixed   $input,  e.g. a json_encoded array like '["cat","dog","bird"]' OR a real PHP array, e.g. array('cat','dog','bird')
	 * @param mixed   $options (optional)formatting parameters
	 * @return string
	 */
	public function filter($input, $options=null) {

		$array = $this->to_array($input);

		// Return an empty string if the input is empty:
		// http://wordpress.org/support/topic/plugin-custom-content-type-manager-displaying-custom-fields-in-conditional-tags?replies=4#post-2537738
		if (empty($array)) {
			return '';
		}
		
		if ( !empty($options) && is_array($options) ) {
			$out = '';
			// format each value
			if ( isset($options[0]) ) {
				foreach ( $array as $k => $v ) {
					$hash['key'] = $k;
					$hash['value'] = $v;
					$out .= CCTM::parse($options[0], $hash);
				}				
			}
			else {
				// ??? user supplied an associative array for options???
				return __('Options array in incorrect format!',CCTM_TXTDOMAIN);
			}

			// wrap the output
			if ( isset($options[1]) ) {
				$hash['content'] = $out;
				return CCTM::parse($options[1], $hash);
			}
		}
		// $options is a string: use a simple string separator
		elseif (!empty($options) && !is_array($options) ) {
			//foreach ( $array as $i => $item ) {
			// $array[$i] = htmlspecialchars($item); // we apply this
			//}
			if ($this->_is_assoc($array)) {
				$array = array_values($array);	
			}
			return implode($options, $array);
		}
		// Default behavior: use a comma
		else {
			return implode(', ', $array);
		}
	}


	/**
	 *
	 *
	 * @return string a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>formatted_list</em> filter converts a JSON array into a formatted string such as an HTML list. See the info page for formatting options.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @param string  $fieldname (optional)
	 * @return string  a code sample
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return "<?php print_custom_field('$fieldname:formatted_list', array('<li>[+value+]</li>','<ul>[+content+]</ul>') ); ?>";
	}


	/**
	 *
	 *
	 * @return string the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Formatted List', CCTM_TXTDOMAIN);
	}


	/**
	 *
	 *
	 * @return string the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/formatted_list_OutputFilter', CCTM_TXTDOMAIN);
	}
}

/*EOF*/