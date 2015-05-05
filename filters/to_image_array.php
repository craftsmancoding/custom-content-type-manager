<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Obscures a string (e.g. an to_image_array address) to make it more difficult for it to 
 * be harvested by bots.
 */

class CCTM_to_image_array extends CCTM_OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	input
	 * @param	mixed	optional arguments
	 * @return mixed
	 */
	public function filter($input, $options=null) {
		$input = $this->to_array($input);
		if ($this->is_array_input) {
			foreach($input as &$item) {
				$item = wp_get_attachment_image_src( $item, $options, true);
			}
			return $input;
		}
		else {
			return wp_get_attachment_image_src( $input[0], $options, true);
		}
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>to_image_array</em> breaks down a referenced image into an array of its component parts: src, width, height.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		if ($is_repeatable) {
			return '<?php 
$images = get_custom_field(\''.$fieldname.':to_image_array\');
foreach ($images as $img) {
	printf(\'<img src="%s" height="%s" width="%s" />\', $img[0], $img[1], $img[2]);
}
?>

<img src="<?php print $src; ?>" height="<?php print $h; ?>" width="<?php print $w ?>" />';
		}
		else {	
			return '<?php 
list($src, $w, $h) = get_custom_field(\''.$fieldname.':to_image_array\');
?>

<img src="<?php print $src; ?>" height="<?php print $h; ?>" width="<?php print $w ?>" />';
		}
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Array of image src, width, height', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/to_image_array_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/