<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Converts a numerical post-id to a full link href
 */

class CCTM_to_link_href extends CCTM_OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	$input
	 * @param	mixed	$options separator to use between multiple instances
	 * @return string
	 */
	public function filter($input, $options='') {
		if (empty($input)) {
			return $options;
		}
		$input = $this->to_array($input);
		if ($this->is_array_input) {
			foreach($input as &$item) {
				if (!is_numeric($item)) {
					$item = sprintf(__('Invalid input. %s operates on post IDs only.', CCTM_TXTDOMAIN), 'to_link_href');
					continue;
				}
				$item = get_permalink($item);
			}
			return $input;
		}
		else {
			if (!is_numeric($input[0])) {
				return sprintf(__('Invalid input. %s operates on post IDs only.', CCTM_TXTDOMAIN),'to_link_href');
			}
			return get_permalink($input[0]);
		}
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>to_link_href</em> filter takes a post ID and converts a post ID into the link href to that post. Optionally, you can supply the href to a page that will be used if no valid input is detected.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		if ($is_repeatable) {
			return '<?php 
$hrefs = get_custom_field(\''.$fieldname.':to_link_href\',\'http://yoursite.com/default/page/\');
foreach($hrefs as $h) {
	printf(\'<a href="%s">Click Here</a><br/>\', $h);
}
?>';
		}
		else {
			return '<a href="<?php print_custom_field(\''.$fieldname.':to_link_href\',\'http://yoursite.com/default/page/\');?>">Click here</a>';
		}	
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Link href only', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/to_link_href_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/