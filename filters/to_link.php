<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Take a numerical post id and converts it to a full anchor tag.
 */

class CCTM_to_link extends CCTM_OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	input: a single post ID or an array of them
	 * @param	string	formatting string OR clickable title
	 * @return mixed
	 */
	public function filter($input, $options=null) {

		$output = '';
		
		// Gotta set the default here due to how print_custom_field calls get_custom_field
		if (empty($options)) {
			$options = '<a href="[+permalink+]" title="[+post_title+]">[+post_title+]</a>';
		}
		elseif(strpos($options,'[+') === false) {
			$options = '<a href="[+permalink+]" title="[+post_title+]">'.$options.'</a>';		
		}
		$input = $this->to_array($input);
		
		if (empty($input)) {
			return '';
		}
		$output = '';
		
		if ($this->is_array_input) {
		
			foreach ($input as &$item) {
				if ($item) {
					//$post = get_post($item);

					if (!is_numeric($item)) {
						$item = sprintf(__('Invalid input. %s operates on post IDs only.', CCTM_TXTDOMAIN), 'to_link');
						continue;
					}
					$post = get_post_complete($item);
					if (!is_array($post)) {
						$item = __('Referenced post not found.', CCTM_TXTDOMAIN);
						continue;
					}
					$link_text = $post['post_title'];
					$item = CCTM::parse($options, $post);
				}
			}
			return implode(', ',$input);
		}
		else {
			if (!is_numeric($input[0])) {
				return sprintf(__('Invalid input. %s operates on post IDs only.', CCTM_TXTDOMAIN),'to_link');
			}		
			$post = get_post_complete($input[0]);
			if (!is_array($post)) {
				return _e('Referenced post not found.', CCTM_TXTDOMAIN);
			}
			return CCTM::parse($options, $post);
		}

	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>to_link</em> filter takes a post ID and converts it into a full anchor tag. Be default, the post title will be used as the clickable text, but you can supply your own text.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return "<?php print_custom_field('$fieldname:to_link'); ?>";
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Full link &lt;a&gt; tag', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/to_link_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/