<?php
/**
 * Patterns are flexible, useful for phone-numbers, IP addresses, etc.
 * This allows us to use Perl regular expressions.
 *
 * @package CCTM_Validator
 *
 */
class CCTM_Rule_pattern extends CCTM_Validator {

	public $options = array(
		'pattern' => '',
		'use_preg_match' => 0,
		'message' => '',
	);

	/**
	 * @return string	a description of what the validation rule is and does.
	 */
	public function get_description() {
		return __('Input must match a pattern of letters or numbers, useful for phone numbers, SKUs, etc. If simple patterns are used, the input must be the same length as the pattern. For more flexible control, use the preg_match option.', CCTM_TXTDOMAIN);		
	}


	/**
	 * @return string	the human-readable name of the validation rules.
	 */
	public function get_name() {
		return __('Pattern', CCTM_TXTDOMAIN);
	}


	/**
	 * Implement this if your validation rule requires some options: this should
	 * return some form elements that will dynamically be shown on the page via 
	 * an AJAX request if this validator is selected.  
	 * Do not include the entire form, just the inputs you need!
	 */
	public function get_options_html() { 

		$use_preg_match_is_checked = '';
		if ($this->use_preg_match) {
			$use_preg_match_is_checked = ' checked="checked"';
		}
			
		$options = '<label class="cctm_label" for="'.$this->get_field_id('pattern').'">'.__('Pattern', CCTM_TXTDOMAIN).'</label>
			<input type="text" name="'.$this->get_field_name('pattern').'" id="'.$this->get_field_id('pattern').'" value="'.htmlspecialchars($this->pattern).'"><br/>
			<span class="cctm_description">'. __('For simple patterns, use the following symbols:', CCTM_TXTDOMAIN) .'<br/>' 
			. __('# : Any digit, 0-9', CCTM_TXTDOMAIN) . '<br/>'
			. __('* : Any letter A-Z', CCTM_TXTDOMAIN) . '<br/>'
			. __('? : Any character', CCTM_TXTDOMAIN) . '<br/>'
			. __('All other characters must match verbatim.', CCTM_TXTDOMAIN) . '<br/>'
			.'</span><br/>
			<input type="checkbox" name="'.$this->get_field_name('use_preg_match').'" id="'.$this->get_field_id('use_preg_match').'" value="1" class="cctm_checkbox" '.$this->is_checked('use_preg_match').' '.$use_preg_match_is_checked.'> 
			<label class="cctm_checkbox_label" for="'.$this->get_field_id('use_preg_match').'">'.__('Use preg_match', CCTM_TXTDOMAIN).'</label><br/>
			<span class="cctm_description">'.__('For complex patterns, use preg_match and enter the entire pattern and its modifiers here, e.g. <code>/^xyz/</code>', CCTM_TXTDOMAIN).'</span>
			<label class="cctm_label" for="'.$this->get_field_id('message').'">'.__('Message', CCTM_TXTDOMAIN).'</label>
			<input type="text" name="'.$this->get_field_name('message').'" id="'.$this->get_field_id('message').'" value="'.htmlspecialchars($this->message).'" style="width:400px;"><br/>
			<span class="cctm_description">'.__('If the field fails validation, this message will be displayed. %s will be replaced by the field name.', CCTM_TXTDOMAIN).'</span>
			';
			
			return $options;
	}
		
	/**
	 * Run the rule: check the user input. Return the (filtered) value that should
	 * be used to repopulate the form.
	 *
	 * @param string 	$input (as it is stored in the database)
	 * @return string
	 */
	public function validate($input) {
		$raw_input = $input;
		//$input = iconv('UTF-8','ISO-8859-1', $input); // doesn't work
		if ($this->use_preg_match) {
			$result = @preg_match($this->pattern, $input);
			// Failed with error
			if ($result === false) {
				$error = error_get_last();	
				$this->error_msg = sprintf(__('Invalid preg_match() pattern defined for the %s field.', CCTM_TXTDOMAIN), $this->get_subject()) . ' ' . $error['message'];
			}
			// Did not match.
			elseif ($result == false) {
				if (empty($this->message)) {
					$this->error_msg = sprintf(__('The %s field must pass the validation rule.', CCTM_TXTDOMAIN), $this->get_subject());	
				}
				else {
					$this->error_msg = sprintf(__($this->message), $this->get_subject());
				}
			}
		}
		else {
			$len1 = strlen($input);
			$len2 = strlen($this->pattern);
			if ($len1 > $len2) {
				$this->error_msg = sprintf(__('The %s field is longer than the defined pattern.', CCTM_TXTDOMAIN), $this->get_subject());	
			}
			elseif($len1 > $len2) {
				$this->error_msg = sprintf(__('The %s field is shorter than the defined pattern.', CCTM_TXTDOMAIN), $this->get_subject());
			}
			else {
				$error_flag = false;
				for ( $i = 0; $i < $len1; $i++ ) {
					$symbol = substr($this->pattern, $i , 1);
					$char = substr($input, $i , 1);
					if ($symbol == '#') {
						if (!is_numeric($char)) {
							$error_flag = true;
						}
					}
					elseif ($symbol == '*') {
						if (!ctype_alpha($char)) {
							$error_flag = true;
						}
					}
					elseif ($symbol == '?') {
						// anything goes
					}
					else {
						if (strtolower($char) != strtolower($symbol)) {
							$error_flag = true;
						}
					}
					
					if ($error_flag) {
						if (empty($this->message)) {
							$this->error_msg = sprintf(__('The %s field must match the pattern defined for this field: '.$this->pattern, CCTM_TXTDOMAIN), $this->get_subject());	
						}
						else {
							$this->error_msg = sprintf(__($this->message), $this->get_subject());
						}
					}
				}
			}
		}
		
		return $raw_input;
	}
	
}
/*EOF*/