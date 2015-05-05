<?php
/**
 * Check to see if the input is a valid URL
 * @package CCTM_Validator
 *
 */
class CCTM_Rule_url extends CCTM_Validator {


	/**
	 * @return string	a description of what the validation rule is and does.
	 */
	public function get_description() {
		return __('Check for a valid URL.', CCTM_TXTDOMAIN);		
	}


	/**
	 * @return string	the human-readable name of the validation rules.
	 */
	public function get_name() {
		return __('URL', CCTM_TXTDOMAIN);
	}
		
	/**
	 * Run the rule: check the user input. Return the (filtered) value that should
	 * be used to repopulate the form.
	 *
	 * @param string 	$input (as it is stored in the database)
	 * @return string
	 */
	public function validate($input) {

        $protocol = '(http://|https://|ftp://|//)';
        $allowed = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)';

        $regex = '^'. $protocol . // must include the protocol
                         '(' . $allowed . '{1,63}\.)+'. // 1 or several sub domains with a max of 63 chars
                         '[a-z]' . '{2,6}'; // followed by a TLD
        if(!preg_match('@'.$regex.'@i', $input)){ 
			$this->error_msg = sprintf(__('The %s field is not a valid URL. The URL must include the protocol, e.g. http://wpcctm.com/', CCTM_TXTDOMAIN), $this->get_subject());
        }
		
		return $input;
	}
	
}
/*EOF*/