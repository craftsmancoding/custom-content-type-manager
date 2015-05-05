<?php
/**
 * @package CCTM_OutputFilter
 * 
 * The raw filter returns the input back out unadulterated. By itself, it does
 * nothing, but forcing a field to use the raw filter will override the default
 * Output Filter set for a field.
 */

class CCTM_raw extends CCTM_OutputFilter {

	/**
	 * Don't show this filter in any dropdown menus for a Default Output Filter
	 */
	public $show_in_menus = false;
	
	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	input
	 * @param	mixed	optional arguments
	 * @return mixed
	 */
	public function filter($input, $options=null) {
		return $input;
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>raw</em> filter simply outputs the input: it does not change the value. You can use it to override the default Output Filter for any custom field.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		if ($is_repeatable) {
			return '<?php $raw_str = get_custom_field(\''.$fieldname.':raw\'); 
	$array = json_decode($raw_str, true);
	foreach ($array as $item) {
		print $item;
	}
?>';
		}
		else {
			return "<?php print_custom_field('$fieldname:raw'); ?>";
		}
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('None', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/raw_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/