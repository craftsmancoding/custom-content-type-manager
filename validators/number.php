<?php
/**
 * Numeric validation can be configured to allow a range of numbers, 
 * @package CCTM_Validator
 *
 */
class CCTM_Rule_number extends CCTM_Validator {

	public $options = array(
		'min' => '',
		'max' => '',
		'allow_negative' => '',
		'allow_decimals' => '',
		'decimal_places' => '',
		'decimal_separator' => '.',
	);

	/**
	 * @return string	a description of what the validation rule is and does.
	 */
	public function get_description() {
		return __('Input must be a number, i.e. something that you could use in arithmetic.', CCTM_TXTDOMAIN);		
	}


	/**
	 * @return string	the human-readable name of the validation rules.
	 */
	public function get_name() {
		return __('Number', CCTM_TXTDOMAIN);
	}


	/**
	 * Implement this if your validation rule requires some options: this should
	 * return some form elements that will dynamically be shown on the page via 
	 * an AJAX request if this validator is selected.  
	 * Do not include the entire form, just the inputs you need!
	 */
	public function get_options_html() { 
		$min = '';
		if (is_numeric($this->min)) {
			$min = $this->min;
		}
		$max = '';
		if (is_numeric($this->max)) {
			$max = $this->max;
		}
		$negative_is_checked = '';
		if ($this->allow_negative) {
			$negative_is_checked = ' checked="checked"';
		}
		$decimals_is_checked = '';
		if ($this->allow_decimals) {
			$decimals_is_checked = ' checked="checked"';
		}
		$decimal_places = '';
		if ($this->decimal_places != '') {
			$decimal_places = (int) $this->decimal_places;
		}
		$comma_selected = '';
		$period_selected = '';
		if ('.' == $this->decimal_separator) {
			$period_selected = ' selected="selected"';
		}
		if (',' == $this->decimal_separator) {
			$comma_selected = ' selected="selected"';
		}	
			
		$options = '<label class="cctm_label" for="'.$this->get_field_id('min').'">'.__('Min Value', CCTM_TXTDOMAIN).'</label>
			<input type="text" name="'.$this->get_field_name('min').'" id="'.$this->get_field_id('min').'" value="'.$min.'">
			<label class="cctm_label" for="'.$this->get_field_id('max').'">'.__('Max Value', CCTM_TXTDOMAIN).'</label>
			<input type="text" name="'.$this->get_field_name('max').'" id="'.$this->get_field_id('max').'" value="'.$max.'"><br/>
			<input type="checkbox" name="'.$this->get_field_name('allow_negative').'" id="'.$this->get_field_id('allow_negative').'" value="1" class="cctm_checkbox" '.$this->is_checked('allow_negative').' '.$negative_is_checked.'>
			<label class="cctm_checkbox_label" for="'.$this->get_field_id('allow_negative').'">'.__('Allow Negative Numbers', CCTM_TXTDOMAIN).'</label><br/>
			<input type="checkbox" name="'.$this->get_field_name('allow_decimals').'" id="'.$this->get_field_id('allow_decimals').'" value="1" class="cctm_checkbox" '.$this->is_checked('allow_decimals').' '.$decimals_is_checked.'> 
			<label class="cctm_checkbox_label" for="'.$this->get_field_id('allow_decimals').'">'.__('Allow Decimals', CCTM_TXTDOMAIN).'</label><br/>
			<label class="cctm_label" for="'.$this->get_field_id('decimal_places').'">'.__('Maximum Decimal Places', CCTM_TXTDOMAIN).'</label>
			<input type="text" name="'.$this->get_field_name('decimal_places').'" id="'.$this->get_field_id('decimal_places').'" value="'.$decimal_places.'"><br/>
			
			<label class="cctm_label" for="'.$this->get_field_id('decimal_separator').'">'.__('Decimal Separator', CCTM_TXTDOMAIN).'</label>
			<select name="'.$this->get_field_name('decimal_separator').'" id="'.$this->get_field_id('decimal_separator').'">
				<option value="."'.$period_selected.'>'.__('Period', CCTM_TXTDOMAIN).' (.)</option>
				<option value=","'.$comma_selected.'>'.__('Comma', CCTM_TXTDOMAIN).' (,)</option>
			</select>
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
	
		// Gotta be a number before we'll even talk to you
		if (!is_numeric($input)) {
			$this->error_msg = sprintf(__('The %s field must be numeric.', CCTM_TXTDOMAIN), $this->get_subject());
		}
		elseif (!$this->allow_negative && $input < 0) {
			$this->error_msg = sprintf(__('The %s field may not be negative.', CCTM_TXTDOMAIN), $this->get_subject());
		}
		elseif ($this->min && $this->max && ($input < $this->min || $input > $this->max)) {
			$this->error_msg = sprintf(__('The %s field must be between %s and %s.', CCTM_TXTDOMAIN), $this->get_subject(), $this->min, $this->max);		
		}
		elseif ($this->min && $input < $this->min) {
			$this->error_msg = sprintf(__('The %s field must be greater than %s.', CCTM_TXTDOMAIN), $this->get_subject(), $this->min);
		}
		elseif ($this->max && $input > $this->max) {
			$this->error_msg = sprintf(__('The %s field must be less than %s.', CCTM_TXTDOMAIN), $this->get_subject(), $this->max);
		}
		// Whole integers only
		elseif (!$this->allow_decimals && strpos($input, $this->decimal_separator) !== false) {
			$this->error_msg = sprintf(__('The %s field must be a whole number (no decimals allowed).', CCTM_TXTDOMAIN), $this->get_subject());					
		}
		// We do some strrev trickery to count # of decimal places
		elseif ($this->allow_decimals && strrpos(strrev($input), $this->decimal_separator) > $this->decimal_places) {
			$this->error_msg = sprintf(__('The %s field cannot contain more than %s decimal places.', CCTM_TXTDOMAIN), $this->get_subject(), $this->decimal_places);			
		}
		
		return $input;
	}
	
}
/*EOF*/