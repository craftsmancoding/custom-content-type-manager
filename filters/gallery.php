<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Converts an array of image ids to HTML.
 */

class CCTM_gallery extends CCTM_OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	input: an integer, an array of integers, or a JSON string representing an array of integers.
	 * @param	string	optional formatting tpl
	 * @return mixed
	 */
	public function filter($input, $options=null) {
		$required_functions = array('getimagesize','imagecreatefromjpeg'
		,'imagecreatefromgif','imagecreatefrompng');
		foreach ($required_functions as $f) {
			if (!function_exists($f) ) {
				return sprintf(__('Missing required function %s'), '<code>'.$f.'</code>');
			}
		}
		
		require_once(CCTM_PATH.'/includes/SummarizePosts.php');
		require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
		
		$tpl = '<div class="cctm_gallery" id="cctm_gallery_[+i+]"><img height="[+height+]" width="[+width+]" src="[+guid+]" title="[+post_title+]" alt="[+alt+]" class="cctm_image" id="cctm_image_[+i+]"/></div>';
		if (!empty($options)) {
			$tpl = $options;
		}

		if (empty($input)) {
			return '';
		}

		$inputs = $this->to_array($input);
		
		$Q = new GetPostsQuery();
		
		$Q->set_include_hidden_fields(true);
		
		// We can't use $Q->get_posts() because MySQL will return results in an arbitrary order.  boo.		
		$output = '';
		$i = 1;
		foreach($inputs as $image_id) {
			$r = $Q->get_post($image_id);
			// Translate
			$r['post_title'] = __($r['post_title']);
			$r['post_content'] = __($r['post_content']);
			$r['post_excerpt'] = __($r['post_excerpt']);			
			
			$image_info = getimagesize($r['guid']);
			$image_type = $image_info[2];
			if( $image_type == IMAGETYPE_JPEG ) {
				$this_image = imagecreatefromjpeg($r['guid']);
			} 
			elseif( $image_type == IMAGETYPE_GIF ) {
				$this_image = imagecreatefromgif($r['guid']);
			} 
			elseif( $image_type == IMAGETYPE_PNG ) {
				$this_image = imagecreatefrompng($r['guid']);
			}
		
			if (isset($r['_wp_attachment_image_alt'])) {
				$r['alt'] = $r['_wp_attachment_image_alt'];
			}
			$r['i'] = $i;
			$r['width'] = imagesx($this_image);
			$r['height'] = imagesy($this_image);
			$output .= CCTM::parse($tpl, $r);
			$i++;
		}
		return $output;
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __("The <em>gallery</em> filter converts a single or an array of image IDs into HTML img tags.", CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return "<?php print_custom_field('".$fieldname.":gallery'); ?>";
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Gallery', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/gallery_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/