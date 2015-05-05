<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Converts input (usually a JSON encoded string) into an array
 */

class CCTM_get_post extends CCTM_OutputFilter {

	/**
	 * Convert a post id to an array represent the post and all its data.
	 *
	 * @param 	mixed integer post_id or an array of post_id's
	 * @param	string	optional field name to return OR a formatting string with [+placeholders+]
	 * @return mixed
	 */
	public function filter($input, $options=null) {

		$input = $this->to_array($input);
		if ($options && is_scalar($options)) {
			$output = '';
		}
		else {
			$output = array();
		}
		if (empty($input)) {
		  return false;
		}
		if ($this->is_array_input) {
			foreach ($input as $k => $item) {
				$item = (int) $item;
				$post = get_post_complete($item);
				if ($options && is_scalar($options)) {
					if (isset($post[$options])) {
						$output .= $post[$options]; 
					}
					else {
						$output .= CCTM::parse($options,$post);
					}
				}
				else {
					$output[] = $post;
				}
			}
			return $output;
		}
		else {
			$input = (int) $input[0];
			if ($options && is_scalar($options)) {
				$post = get_post_complete($input);
				if (isset($post[$options])) {
					return $post[$options]; 
				}
				return CCTM::parse($options,$post);
			}
			else {
				return get_post_complete($input);						
			}
		}
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __("The <em>get_post</em> retrieves a post by its ID.  Unlike WordPress's get_post() function, this filter appends all custom field data.", CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return '<?php 
$my_post = get_custom_field("'.$fieldname.':get_post");
print $my_post["post_title"]; 
print $my_post["my_custom_field"];
// ... etc ...
?>';
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Get Post', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/get_post_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/