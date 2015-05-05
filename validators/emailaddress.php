<?php
/**
 * Check to see if the input is a valid email address
 * @package CCTM_Validator
 *
 */
class CCTM_Rule_emailaddress extends CCTM_Validator {


	/**
	 * @return string	a description of what the validation rule is and does.
	 */
	public function get_description() {
		return __('Check for a valid email address.', CCTM_TXTDOMAIN);		
	}


	/**
	 * @return string	the human-readable name of the validation rules.
	 */
	public function get_name() {
		return __('Email', CCTM_TXTDOMAIN);
	}
		
	/**
	 * Run the rule: check the user input. Return the (filtered) value that should
	 * be used to repopulate the form.
	 *
	 * @param string 	$input (as it is stored in the database)
	 * @return string
	 */
	public function validate($input) {
		if (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $input)) {
			$this->error_msg = sprintf(__('The %s field is not a valid email address.', CCTM_TXTDOMAIN), $this->get_subject());
		}
		
		return $input;
	}
	
}
/*EOF*/