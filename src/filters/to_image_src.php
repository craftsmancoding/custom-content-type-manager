<?php
/**
 * 
 * Given a post_id (or an array of them), return the src for the image(s).
 */
namespace CCTM\Filters;
class to_image_src extends OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	input (an image post ID), or an array of post IDs
	 * @param	mixed	default image src if no image is available
	 * @return mixed either the src of one image, or an array of src's
	 */
	public function filter($input, $options='') {
		if (is_array($options)) {
			$options = $options[0];
		}

		$input = $this->to_array($input);

		if ($this->is_array_input) {
            if (empty($input)) return array($options);
			foreach($input as &$item) {
				if (!is_numeric($item)) {
					$item = sprintf(__('Invalid input. %s operates on post IDs only.', CCTM_TXTDOMAIN), 'to_image_src');
					continue;
				}

				list($item, $h, $w) = wp_get_attachment_image_src($item, null, true);
				if (empty($item)) {
					$item = $options; // default image
				}
			}
			
			return $input;
		}
		elseif(isset($input[0])) {
			if (!is_numeric($input[0])) {
				return sprintf(__('Invalid input. %s operates on post IDs only.', CCTM_TXTDOMAIN), 'to_image_src');
			}

			list($src, $h, $w) = wp_get_attachment_image_src($input[0], null, true);
			if (empty($src)) {
				return $options;
			}
			else {
				return $src;
			}		
		}
		else {
			return $options;
		}
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __("The <em>to_image_src</em> filter converts an image id into the full URL to the image. Use the this filter if you want to return the full path to the image, for example for use in your own image tag or link.", CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		if ($is_repeatable) {
			return '<?php $images = get_custom_field(\''.$fieldname.':to_image_src\'); 
foreach ($images as $img) {
	printf(\'<img src="%s"/>\', $img);
}
?>';
		}
		else {
			return '<img src="<?php print_custom_field(\''.$fieldname.':to_image_src\'); ?>" />';
		}	
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Image src', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/to_image_src_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/