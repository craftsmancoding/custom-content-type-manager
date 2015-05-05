<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Retrieves an excerpt from the input string, separating either via a string separator 
 * (e.g. <!--more->) OR using an integer to denote the max number of words you'd like 
 * the excerpt to contain.
 */

class CCTM_excerpt extends CCTM_OutputFilter {

	/**
	 * @param	string	$str long string 
	 * @param	mixed	$separator either integer or splitting string
	 */
	private function _get_excerpt($str, $separator) {
		$output = do_shortcode(trim($str));
		// Strip space
		$output = preg_replace('/\s\s+/', ' ', $output);
		$output = preg_replace('/\n/', ' ', $output);
		// Count the number of words
		if (is_integer($separator)) {
			// Strip HTML *before* we count the words.
			$output = strip_tags($output);
			$words_array = explode(' ', $output);			
			$max_word_cnt = $separator;
			$output = implode(' ', array_slice($words_array, 0, $max_word_cnt)) . '&#0133;';
		}
		// Split on a separator
		else {
			$parts = explode($separator, $output);
			$output = $parts[0];
			// Strip HTML *after* we split on the separator
			$output = trim(strip_tags($output)). '&#0133;';
		}
		
		return $output;	
	}

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	input
	 * @param	string	optional arguments: if it's an integer, it represents a word count. If It's a string, it's the string to slice on (default '<!--more-->')
	 * @return mixed matches the input
	 */
	public function filter($input, $options='<!--more-->') {
		if (!is_scalar($options)) {
			$options = '<!--more-->';
		}
		$input = $this->to_array($input);
		
		if ($this->is_array_input) {
			foreach($input as &$item) {
				if (empty($item)) {
					continue;
				}
				$item = $this->_get_excerpt($item, $options);
			}
			return $input;
		}
		else {
			return $this->_get_excerpt($input[0], $options);
		}
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>excerpt</em> takes a long string and returns a shorter excerpt of it, either chopping the input on the separator string or limiting it to a given number of words.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return "<?php print_custom_field('$fieldname:excerpt', 100); ?>";
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Excerpt', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/excerpt_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/