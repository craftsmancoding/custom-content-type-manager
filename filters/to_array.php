<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Converts input (usually a JSON encoded string) into an array
 */

class CCTM_to_array extends CCTM_OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	$input, usually a string representing a JSON-encoded array, but a real PHP array is ok too.
	 * @param	string	$options optional arguments, to_array accepts the name of an Output Filter
	 * @return mixed
	 */
	public function filter($input, $options=null) {
		$the_array = $this->to_array($input);		
		// Apply secondary optional filter to each item in the array.
		if ($options && is_scalar($options)) {
			foreach ($the_array as &$item) {

				$item = CCTM::filter($item, $options);
			}
		}
		return $the_array;
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>to_array</em> filter converts a JSON encoded string to a PHP array. It should be used on any multi-select field or any other field that stores multiple values.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return '<?php 
$my_array = get_custom_field(\''.$fieldname.':to_array\');
foreach ($my_array as $item) {
	print $item;
}
?>';
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Array', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/to_array_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/