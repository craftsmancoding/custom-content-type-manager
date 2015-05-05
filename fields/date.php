<?php
/**
 * CCTM_date
 *
 * This handles all types of time-based input, including dates, date-times, and times.
 * 3 javascript files will be loaded up to support all possible Date Formats
 *
 * Date: standard jQuery datepicker: http://jqueryui.com/demos/datepicker/ 
 * Datetime: based on http://razum.si/jQuery-calendar/TimeCalendar.html 
 * Time: based on https://github.com/perifer/timePicker
 *
 * @package CCTM_FormElement
 */


class CCTM_date extends CCTM_FormElement
{
	public $props = array(
		'label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra' => '',
		'date_format' => '',
		'default_value' => '',
		'required' => '',
		'evaluate_default_value' => 0,
		// 'type' => '', // auto-populated: the name of the class, minus the CCTM_ prefix.
	);

	public $datetype = 'date';  // either date, datetime, time : controls which JS gets loaded

	//------------------------------------------------------------------------------
	/**
	 * Add some necessary Javascript/CSS.
	 */
	public function admin_init($fieldlist=array()) {
		// Standard Datepicker		
		wp_enqueue_script( 'jquery-ui-datepicker', CCTM_URL . '/js/datepicker.js', 'jquery-ui-core');
		// Datetime Picker
		//wp_enqueue_script( 'jquery-datetimepicker', CCTM_URL . '/js/datetime.js', 'jquery');
		// Timepicker: TODO: fix
		//wp_enqueue_script( 'jquery-timepicker', CCTM_URL . '/js/timepicker.js', 'jquery-ui-datepicker');
		// My styles
		wp_register_style('cctm-datetimepicker', CCTM_URL . '/css/date.css');
		wp_enqueue_style('cctm-datetimepicker');
	}


	//------------------------------------------------------------------------------
	/**
	 * This function provides a name for this type of field. This should return plain
	 * text (no HTML). The returned value should be localized using the __() function.
	 *
	 * @return string
	 */
	public function get_name() {
		return __('Date and Time', CCTM_TXTDOMAIN);
	}


	//------------------------------------------------------------------------------
	/**
	 * This function gives a description of this type of field so users will know
	 * whether or not they want to add this type of field to their custom content
	 * type. The returned value should be localized using the __() function.
	 *
	 * @return string text description
	 */
	public function get_description() {
		return __('Use these fields to store dates and times.', CCTM_TXTDOMAIN);
	}


	//------------------------------------------------------------------------------
	/**
	 * This function should return the URL where users can read more information about
	 * the type of field that they want to add to their post_type. The string may
	 * be localized using __() if necessary (e.g. for language-specific pages)
	 *
	 * @return string  e.g. http://www.yoursite.com/some/page.html
	 */
	public function get_url() {
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Date';
	}


	//------------------------------------------------------------------------------
	/**
	 * Optionally evals the default value
	 *
	 * @return string HTML for the field
	 */
	public function get_create_field_instance() {

		if ($this->evaluate_default_value ) {
			$default_value = $this->default_value;
			$this->default_value = eval("return $default_value;");

		}

		if ($this->is_repeatable) {
			$this->default_value = json_encode(array($this->default_value));
		}

		return $this->get_edit_field_instance($this->default_value)
			. '<input type="hidden" name="_cctm_is_create" value="1" />';;
	}


	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @param mixed   $current_value current value for this field.
	 * @return string
	 */
	public function get_edit_field_instance($current_value) {

		$this->id      = str_replace(array('[',']',' '), '_', $this->name);

		$fieldtpl = '';
		$wrappertpl = '';

		// Figure out which tpls to load
		if (in_array($this->date_format, array('yyyy-mm-dd hh:mm:ss','mm/dd/yy hh:mm','mm/dd/yy hh:mm am'))) {
			$this->datetype = 'datetime';
		}
		elseif (in_array($this->date_format, array('show24Hours: true, step: 10','show24Hours: true, step: 15','show24Hours: true, step: 30','show24Hours: false, step: 10','show24Hours: false, step: 15','show24Hours: false, step: 30'))) {
			$this->datetype = 'time';
		}
		else {
			$this->datetype = 'date';
		}

		// Multi-version of the field
		if ($this->is_repeatable) {
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_'.$this->datetype.'_multi.tpl'
				)
			);

			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_'.$this->datetype.'_multi.tpl'
					, 'fields/wrappers/_text_multi.tpl'
				)
			);

			$this->i = 0;
			$values = $this->get_value($current_value,'to_array');
			//die(print_r($values,true));
			$this->content = '';
			foreach ($values as $v) {
				$this->value = htmlspecialchars( html_entity_decode($v) );
				$this->content .= CCTM::parse($fieldtpl, $this->get_props());
				$this->i   = $this->i + 1;
			}

		}
		// Singular
		else {

			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_'.$this->datetype.'.tpl'
					, 'fields/elements/_default.tpl'
				)
			);

			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_'.$this->datetype.'.tpl'
					, 'fields/wrappers/_default.tpl'
				)
			);

			$this->value = htmlspecialchars(html_entity_decode($this->get_value($current_value,'to_string')));
			$this->content = CCTM::parse($fieldtpl, $this->get_props());
		}


		$this->add_label = __('Add', CCTM_TXTDOMAIN);
		return CCTM::parse($wrappertpl, $this->get_props());
	}


	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @param mixed   $def field definition; see the $props array
	 * @return string
	 */
	public function get_edit_field_definition($def) {

		$is_checked = '';
		if (isset($def['evaluate_default_value']) && $def['evaluate_default_value'] == 1) {
			$is_checked = 'checked="checked"';
		}

		// Standard
		$out = $this->format_standard_fields($def);


		// Option - select: populate the array
		// dates
		$date_format = array();
		$date_format['mm/dd/yy']        = '';
		$date_format['yy-mm-dd']        = ''; // note this is really yyyy-mm-dd
		$date_format['d M, y']         = '';
		$date_format['d MM, y']        = '';
		$date_format['DD, d MM, yy']       = '';
		$date_format["'day' d 'of' MM 'in the year' yy"] = '';
		// datetimes
		$date_format['yyyy-mm-dd hh:mm:ss'] = '';
		$date_format['mm/dd/yy hh:mm'] = '';
		$date_format['mm/dd/yy hh:mm am'] = '';
		// times
		$date_format['show24Hours: true, step: 10'] = '';
		$date_format['show24Hours: true, step: 15'] = '';
		$date_format['show24Hours: true, step: 30'] = '';
		$date_format['show24Hours: false, step: 10'] = '';
		$date_format['show24Hours: false, step: 15'] = '';
		$date_format['show24Hours: false, step: 30'] = '';

		
		// Date
		if ( $def['date_format'] == 'mm/dd/yy' ) {
			$date_format['mm/dd/yy'] = 'selected="selected"';
		}
		if ( $def['date_format'] == 'yy-mm-dd' ) {
			$date_format['yy-mm-dd'] = 'selected="selected"';
		}
		if ( $def['date_format'] == 'd M, y' ) {
			$date_format['d M, y'] = 'selected="selected"';
		}
		if ( $def['date_format'] == 'd MM, y' ) {
			$date_format['d MM, y'] = 'selected="selected"';
		}
		if ( $def['date_format'] == 'DD, d MM, yy' ) {
			$date_format['DD, d MM, yy'] = 'selected="selected"';
		}
		if ( $def['date_format'] == "'day' d 'of' MM 'in the year' yy" ) {
			$date_format["'day' d 'of' MM 'in the year' yy"] = 'selected="selected"';
		}
		// Datetime
		if ( $def['date_format'] == 'yyyy-mm-dd hh:mm:ss' ) {
			$date_format['yyyy-mm-dd hh:mm:ss'] = 'selected="selected"';
		}
		if ( $def['date_format'] == 'mm/dd/yy hh:mm' ) {
			$date_format['mm/dd/yy hh:mm'] = 'selected="selected"';
		}
		if ( $def['date_format'] == 'mm/dd/yy hh:mm am' ) {
			$date_format['mm/dd/yy hh:mm am'] = 'selected="selected"';
		}
		// Time
		if ( $def['date_format'] == 'show24Hours: true, step: 10' ) {
			$date_format['show24Hours: true, step: 10'] = 'selected="selected"';
		}
		if ( $def['date_format'] == 'show24Hours: true, step: 15' ) {
			$date_format['show24Hours: true, step: 15'] = 'selected="selected"';
		}
		if ( $def['date_format'] == 'show24Hours: true, step: 30' ) {
			$date_format['show24Hours: true, step: 30'] = 'selected="selected"';
		}		
		if ( $def['date_format'] == 'show24Hours: false, step: 10' ) {
			$date_format['show24Hours: false, step: 10'] = 'selected="selected"';
		}
		if ( $def['date_format'] == 'show24Hours: false, step: 15' ) {
			$date_format['show24Hours: false, step: 15'] = 'selected="selected"';
		}
		if ( $def['date_format'] == 'show24Hours: false, step: 30' ) {
			$date_format['show24Hours: false, step: 30'] = 'selected="selected"';
		}
		
		// Options
		$out .= '
			<div class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span>'. __('Options', CCTM_TXTDOMAIN).'</span></h3>
				<div class="inside">';

		// Evaluate Default Value (use PHP eval)
		$out .= '<div class="'.self::wrapper_css_class .'" id="evaluate_default_value_wrapper">
				 <label for="evaluate_default_value" class="cctm_label cctm_checkbox_label" id="evaluate_default_value_label">'
			. __('Use PHP eval to calculate the default value? (Omit the php tags, e.g. <code>date(\'Y-m-d\')</code>).', CCTM_TXTDOMAIN) .
			'</label>
				 <br />
				 <input type="checkbox" name="evaluate_default_value" class="cctm_checkbox" id="evaluate_default_value" value="1" '. $is_checked.'/> '
			.$this->descriptions['evaluate_default_value'].'
			 	</div>';

		// Date Format
		$out .= '<div class="'.self::wrapper_css_class .'" id="date_format_wrapper">
			 		<label for="date_format" class="'.self::label_css_class.'">'
			.__('Date Format', CCTM_TXTDOMAIN) .'</label>
					<select id="date_format" name="date_format">
						<optgroup label="'.__('Date', CCTM_TXTDOMAIN).'">
							<option value="mm/dd/yy" '.$date_format['mm/dd/yy'].'>'.__('Default',CCTM_TXTDOMAIN).' - mm/dd/yy</option>
							<option value="yy-mm-dd" '.$date_format['yy-mm-dd'].'>MySQL - yyyy-mm-dd</option>
							<option value="d M, y" '.$date_format['d M, y'].'>'.__('Short',CCTM_TXTDOMAIN).' - d M, y</option>
							<option value="d MM, y" '.$date_format['d MM, y'].'>'.__('Medium',CCTM_TXTDOMAIN).' - d MM, y</option>
							<option value="DD, d MM, yy" '.$date_format['DD, d MM, yy'].'>'.__('Full',CCTM_TXTDOMAIN).' - DD, d MM, yy</option>
							<option value="\'day\' d \'of\' MM \'in the year\' yy" '.$date_format["'day' d 'of' MM 'in the year' yy"].'>'.__('With text - \'day\' d \'of\' MM \'in the year\' yy',CCTM_TXTDOMAIN).'</option>
						</optgroup>
						<!-- optgroup label="'.__('Date + Time', CCTM_TXTDOMAIN).'">
							<option value="yyyy-mm-dd hh:mm:ss" '.$date_format['yyyy-mm-dd hh:mm:ss'].'>MySQL - yyyy-mm-dd hh:mm:ss '.__('24 hour',CCTM_TXTDOMAIN).'</option>
							<option value="mm/dd/yy hh:mm" '.$date_format['mm/dd/yy hh:mm'].'>'.__('Full',CCTM_TXTDOMAIN).' - mm/dd/yy hh:mm '.__('24 hour',CCTM_TXTDOMAIN).'</option>
							<option value="mm/dd/yy hh:mm am" '.$date_format['mm/dd/yy hh:mm am'].'>'.__('Full',CCTM_TXTDOMAIN).' - mm/dd/yy hh:mm AM/PM</option>
						</optgroup>
						<optgroup label="'.__('Time', CCTM_TXTDOMAIN).'">
							<option value="show24Hours: true, step: 10" '.$date_format['show24Hours: true, step: 10'].'>hh:mm - '.__('24 hour',CCTM_TXTDOMAIN).', '.__('10 minute intervals', CCTM_TXTDOMAIN).'</option>
							<option value="show24Hours: true, step: 15" '.$date_format['show24Hours: true, step: 15'].'>hh:mm - '.__('24 hour',CCTM_TXTDOMAIN).', '.__('15 minute intervals', CCTM_TXTDOMAIN).'</option>
							<option value="show24Hours: true, step: 30" '.$date_format['show24Hours: true, step: 30'].'>hh:mm - '.__('24 hour',CCTM_TXTDOMAIN).', '.__('30 minute intervals', CCTM_TXTDOMAIN).'</option>
							<option value="show24Hours: false, step: 10" '.$date_format['show24Hours: false, step: 10'].'>hh:mm - AM/PM, '.__('10 minute intervals', CCTM_TXTDOMAIN).'</option>
							<option value="show24Hours: false, step: 15" '.$date_format['show24Hours: false, step: 15'].'>hh:mm - AM/PM, '.__('15 minute intervals', CCTM_TXTDOMAIN).'</option>
							<option value="show24Hours: false, step: 30" '.$date_format['show24Hours: false, step: 30'].'>hh:mm - AM/PM, '.__('30 minute intervals', CCTM_TXTDOMAIN).'</option>
						</optgroup-->
					</select>
					<span class="cctm_description">'.__('If you need to sort your dates, it is recommended to use the MySQL date formats. Change how the date displays using Output Filters in your template files. Custom formatting options can be used by customizing the Javascript constructors in the .tpl files.', CCTM_TXTDOMAIN).'</span>
				</div>';
			 	
		$out .= '</div><!-- /inside -->
			</div><!-- /postbox -->';
				 	
		// Validations / Required
		$out .= $this->format_validators($def);
		
		// Output Filter
		$out .= $this->format_available_output_filters($def);

		return $out;
	}

    //------------------------------------------------------------------------------
    /**
     * Show the default date (if avail.) and the date format
     * @return string
     */
    public function get_options_desc() {
        $out = '';
        if (!empty($this->props['date_format'])) {
            $out .= __('Date Format',CCTM_TXTDOMAIN) . ': '.$this->props['date_format'] .'<br/>';
        }
        if (!empty($this->props['default_value'])) {
            $out .= $this->props['default_value'] .'<em>('.__('default',CCTM_TXTDOMAIN).')</em>';
        }
        return $out;
    }

}


/*EOF*/