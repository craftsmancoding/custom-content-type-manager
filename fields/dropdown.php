<?php
/**
 * CCTM_dropdown
 *
 * Implements an HTML select element with options (single select).
 *
 * @package CCTM_FormElement
 */
class CCTM_dropdown extends CCTM_FormElement
{
	public $props = array(
		'label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra' => '',
		'default_value' => '',
		'required' => '',
		'options' => array(),
		'values' => array(), // only used if use_key_values = 1
		'use_key_values' => 0, // if 1, then 'options' will use key => value pairs.
		'display_type' => 'dropdown', // dropdown|radio
		// 'type' => '', // auto-populated: the name of the class, minus the CCTM_ prefix.

	);

	//------------------------------------------------------------------------------
	/**
	 * Register the appropriatejs
	 */
	public function admin_init($fieldlist=array()) {
		wp_register_script('cctm_dropdown', CCTM_URL.'/js/dropdown.js', array('jquery'));
		wp_enqueue_script('cctm_dropdown');
	}


	//------------------------------------------------------------------------------
	/**
	 * This function provides a name for this type of field. This should return plain
	 * text (no HTML). The returned value should be localized using the __() function.
	 *
	 * @return string
	 */
	public function get_name() {
		return __('Dropdown', CCTM_TXTDOMAIN);
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
		return __('Dropdown fields implement a <select> element which lets you select a single item. "Extra" parameters, e.g. "alt" can be specified in the definition.', CCTM_TXTDOMAIN);
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
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Dropdown';
	}


	//------------------------------------------------------------------------------
	/**
	 * Get an instance of this field (used when you are creating or editing a post
	 * that uses this type of custom field).
	 *
	 * @param string  $current_value of the field for the current post
	 * @return string
	 */
	public function get_edit_field_instance($current_value) {

		$current_value = $this->get_value($current_value, 'to_string');
		
		// Format for Radio buttons
		if ( $this->display_type == 'radio' ) {

			$optiontpl = CCTM::load_tpl(
				array('fields/options/'.$this->name.'.tpl'
					, 'fields/options/_radio.tpl'
				)
			);
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_radio.tpl'
					, 'fields/elements/_default.tpl'
				)
			);
			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_radio.tpl'
					, 'fields/wrappers/_default.tpl'
				)
			);
		}
		// For regular selects / dropdowns
		else {
			$optiontpl = CCTM::load_tpl(
				array('fields/options/'.$this->name.'.tpl'
					, 'fields/options/_option.tpl'
				)
			);
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_dropdown.tpl'
					, 'fields/elements/_default.tpl'
				)
			);
			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_'.$this->type.'.tpl'
					, 'fields/wrappers/_default.tpl'
				)
			);
		}

		// Some error messaging: the options thing is enforced at time of def creation, so
		// we shouldn't ever need to enforce it here, but just in case...
		if ( !isset($this->options) || !is_array($this->options) ) {
			return sprintf('<p><strong>%$1s</strong> %$2s %$3s</p>'
				, __('Custom Content Error', CCTM_TXTDOMAIN)
				, __('No options supplied for the following custom field: ', CCTM_TXTDOMAIN)
				, $data['name']
			);
		}


		// Get the options.  This currently is not skinnable.
		// $this->props['options'] is already bogarted by the definition.
		// Add an empty <option> for non-required dropdowns
		$this->all_options = '';
		if ($this->display_type != 'radio' && (!$this->required)) {
			$hash['value'] = '';
			$hash['option'] = '';
			$this->all_options .= CCTM::parse($optiontpl, $hash);
		}
		
		// Handle SQL queries
		if ($this->is_sql) {
			if (empty($this->alternate_input)) {
				return __('Alternate input must not be empty if SQL box is checked.', CCTM_TXTDOMAIN);
			}
			else {
				global $wpdb;
				global $table_prefix;
				$wpdb->hide_errors();
				$query = CCTM::parse($this->alternate_input, array('table_prefix'=>$table_prefix));
				//return $query;
				$results = $wpdb->get_results($query, ARRAY_N);
				if ($wpdb->last_error) {
					return $wpdb->last_error;
				}
				$options = array();
				$values = array();
				foreach ($results as $r_i => $r) {
					$options[$r_i] = $r[0];
					if (isset($r[1])) {
						$values[$r_i] = $r[1];
					}
					else {
						$values[$r_i] = $r[0];
					}
				}
				$this->use_key_values = 1; // see https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=541
				$this->set_prop('options', $options);
				$this->set_prop('values', $values);
			}
		}
		// Bulk input
		elseif (!$this->is_sql && !empty($this->alternate_input)) {
			$options = array();
			$values = array();
			$mixed = explode("\n",$this->alternate_input);
			foreach($mixed as $m_i => $m) {
				$line = explode('||',$m);
				$options[$m_i] = trim($line[0]);
				if (isset($line[1])) {
					$values[$m_i] = trim($line[1]);
				}
				else {
					$values[$m_i] = trim($line[0]);
				}
			}
			$this->set_prop('options', $options);
			$this->set_prop('values', $values);			
		}

		
		$opt_cnt = count($this->options);

		// Populate the options
		for ( $i = 0; $i < $opt_cnt; $i++ ) {
			$hash = $this->get_props();

			// just in case the array isn't set
			$hash['option'] = '';
			if (isset($this->options[$i])) {
				$hash['option'] = htmlspecialchars($this->options[$i]);
			}
			$hash['value'] = '';
			if (isset($this->values[$i])) {
				$hash['value'] = htmlspecialchars($this->values[$i]);
			}
			// Simplistic behavior if we don't use key=>value pairs
			if ( !$this->use_key_values ) {
				$hash['value'] = $hash['option'];
			}

			$hash['is_selected'] = '';
			$hash['is_checked'] = '';

			if ( trim($current_value) == trim($hash['value']) ) {
				$hash['is_checked'] = 'checked="checked"';
				$hash['is_selected'] = 'selected="selected"';
			}

			$hash['i'] = $i;
			$hash['id'] = $this->name;

			$this->all_options .= CCTM::parse($optiontpl, $hash);
		}



		// Populate the values (i.e. properties) of this field
		$this->id      = str_replace(array('[',']',' '), '_', $this->name);
		$this->value    = htmlspecialchars( html_entity_decode($current_value) );

		// wrap
        $this->set_prop('value', $current_value);
		$this->content = CCTM::parse($fieldtpl, $this->get_props());
		return CCTM::parse($wrappertpl, $this->get_props());

	}


	//------------------------------------------------------------------------------
	/**
	 * Note that the HTML in $option_html should match the JavaScript version of
	 * the same HTML in js/dropdown.js (see the append_dropdown_option() function).
	 * I couldn't think of a clean way to do this, but the fundamental problem is
	 * that both PHP and JS need to draw the same HTML into this form:
	 * PHP draws it when an existing definition is *edited*, whereas JS draws it
	 * when you dynamically *create* new dropdown options.
	 *
	 * @param array   $def nested array of existing definition.
	 * @return string
	 */
	public function get_edit_field_definition($def) {

		// Standard
		$out = $this->format_standard_fields($def,false);

		$is_checked = '';
		$is_sql_checked = '';
		$readonly_str = ' readonly="readonly"';
		if (isset($def['use_key_values']) && $def['use_key_values']) {
			$is_checked = 'checked="checked"';
			$readonly_str = '';
		}
		if (isset($def['is_sql']) && $def['is_sql']) {
			$is_sql_checked = 'checked="checked"';
		}
		// Options
		$out .= '
			<div class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span>'. __('Options', CCTM_TXTDOMAIN).'</span></h3>
				<div class="inside">
					<table><tr><td width="600" style="vertical-align:top">';

		// Use Key => Value Pairs?  (if not, the simple usage is simple options)
		$out .= '
			<input type="hidden" name="use_key_values" value="0"/>
			<div class="'.self::wrapper_css_class .'" id="use_key_values_wrapper">
				 <label for="use_key_values" class="cctm_label cctm_checkbox_label" id="use_key_values_label">'
			. __('Distinct options/values?', CCTM_TXTDOMAIN) .
			'</label>
				 <br />
				 <input type="checkbox" name="use_key_values" class="cctm_checkbox" id="use_key_values" value="1" onclick="javascript:toggle_readonly();" '. $is_checked.'/> <span>'.$this->descriptions['use_key_values'].'</span>

			 	</div>';

		// OPTIONS
		$option_cnt = 0;
		if (isset($def['options'])) {
			$option_cnt = count($def['options']);
		}

		// using the parse function because this got too crazy with escaping single quotes
		$hash = array();
		$hash['option_cnt']  = $option_cnt;
		$hash['delete']   = __('Delete');
		$hash['options']   = __('Options', CCTM_TXTDOMAIN);
		$hash['values']   = __('Stored Values', CCTM_TXTDOMAIN);
		$hash['add_option']  = __('Add Option', CCTM_TXTDOMAIN);
		$hash['set_as_default'] = __('Set as Default', CCTM_TXTDOMAIN);

		$tpl = '
			<script type="text/javascript">
				jQuery(function() {
					jQuery( "#dropdown_options2" ).sortable();
					// jQuery( "#dropdown_options2" ).disableSelection();
				});			
			</script>
			<table id="dropdown_options">
				<thead>
				<td scope="col" id="sorter" class=""  style="">&nbsp;</td>	
				<td width="200"><label for="options" class="cctm_label cctm_select_label" id="cctm_label_options">[+options+]</label></td>
				<td width="200"><label for="options" class="cctm_label cctm_select_label" id="cctm_label_options">[+values+]</label></td>
				<td>
				 <span class="button" onclick="javascript:append_dropdown_option(\'dropdown_options\',\'[+delete+]\',\'[+set_as_default+]\',\'[+option_cnt+]\');">[+add_option+]</span>
				</td>
				</thead>
				<tbody id="dropdown_options2">';

		$out .= CCTM::parse($tpl, $hash);

		// this html should match up with the js html in dropdown.js
		$option_html = '
			<tr id="%s">
				<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
				<td><input type="text" name="options[]" id="option_%s" value="%s"/></td>
				<td><input type="text" name="values[]" id="value_%s" value="%s" class="possibly_gray"'.$readonly_str.'/></td>
				<td><span class="button" onclick="javascript:remove_html(\'%s\');">%s</span>
				<span class="button" onclick="javascript:set_as_default(\'%s\');">%s</span></td>
			</tr>';


		$opt_i = 0; // used to uniquely ID options.
		if ( !empty($def['options']) && is_array($def['options']) ) {

			$opt_cnt = count($def['options']);
			for ( $i = 0; $i < $opt_cnt; $i++ ) {
				// just in case the array isn't set
				$option_txt = '';
				if (isset($def['options'][$i])) {
					$option_txt = htmlspecialchars(trim($def['options'][$i]));
				}
				$value_txt = '';
				if (isset($def['values'][$i])) {
					$value_txt = htmlspecialchars(trim($def['values'][$i]));
				}

				$option_css_id = 'cctm_dropdown_option'.$opt_i;
				$out .= sprintf($option_html
					, $option_css_id
					, $opt_i
					, $option_txt
					, $opt_i
					, $value_txt
					, $option_css_id, __('X')
					, $opt_i
					, __('Set as Default')
				);
				$opt_i = $opt_i + 1;
			}
		}

		$out .= '
			</tbody>
		</table>'; // close id="dropdown_options"

		// Display as Radio Button or as Dropdown?
		$out .= '<div class="'.self::wrapper_css_class .'" id="display_type_wrapper">
				 <label class="cctm_label cctm_checkbox_label" id="display_type_label">'
			. __('How should the field display?', CCTM_TXTDOMAIN) .
			'</label>
				 <br />
				 <input type="radio" name="display_type" class="cctm_radio" id="display_type_dropdown" value="dropdown" '. CCTM::is_radio_selected('dropdown', CCTM::get_value($this->props, 'display_type', 'dropdown') ).'/>
				 <label for="display_type_dropdown" class="cctm_label cctm_radio_label" id="display_type_dropdown_label">'
			. __('Dropdown', CCTM_TXTDOMAIN) .
			'</label><br />
				 <input type="radio" name="display_type" class="cctm_radio" id="display_type_radio" value="radio" '. CCTM::is_radio_selected('radio', CCTM::get_value($this->props, 'display_type', 'dropdown')).'/>
				 <label for="display_type_radio" class="cctm_label cctm_radio_label" id="display_type_radio_label">'
			. __('Radio Button', CCTM_TXTDOMAIN) .
			'</label><br />
			 	</div>';
			 	
		// Secondary Input options
		$out .= '</td><td style="vertical-align:top">
			<label class="cctm_label cctm_textarea_label" id="advanced_label">'
			. __('Alternate Input', CCTM_TXTDOMAIN) .
			'</label>
			<span>'.__('Use this input if you want to define options in bulk, one option per line.
				Separate options and values using double-pipes "||" with the visible option on the left, the corresponding value
				to be stored on the right (if present).  You may also enter a valid MySQL query. This field overrides 
				other inputs!', CCTM_TXTDOMAIN).'</span><br/>
			<textarea name="alternate_input" id="alternate_input" cols="50" rows="10">'.
			CCTM::get_value($def,'alternate_input')
			.'</textarea>';

		// Execute as MySQL?
		$out .= '<div class="'.self::wrapper_css_class .'" id="is_sql_wrapper">
				<input type="hidden" name="is_sql" value="0"/>
				 <input type="checkbox" name="is_sql" class="cctm_checkbox" id="is_sql" value="1"'. $is_sql_checked.'/> 				 <label for="is_sql" class="cctm_label cctm_checkbox_label" id="is_sql_label">'
				 .__('Execute as a MySQL query?', CCTM_TXTDOMAIN).'</label> <span>'.__('Select up to 2 columns: the 1st column will be the visible label and the 2nd column (if present) will represent the value stored in the database.
				 	Use [+table_prefix+] instead of hard-coding your WordPress database table prefix.',CCTM_TXTDOMAIN).'</span>
			 	</div>';
			
			
		$out .= '
					</td></tr></table>		
				</div><!-- /inside -->
			</div><!-- /postbox -->';

		// Validations / Required
		$out .= $this->format_validators($def,false);

		// Output Filter
		$out .= $this->format_available_output_filters($def);

		return $out;
	}

    //------------------------------------------------------------------------------
    /**
     * The option desc. here is simply a list of the options OR the SQL query alt.
     */
    public function get_options_desc() {
        if (!empty($this->props['options'])) {
            $options = implode(', ',$this->props['options']);
        }
        else {
            $options = $this->props['alternate_input'];        
        }
        if (strlen($options) > 50) {
            $options = substr($options, 0, 50). '&hellip;';
        }
        return $options;
    }
    
	//------------------------------------------------------------------------------
	/**
	 * Validate and sanitize any submitted data. Used when editing the definition for
	 * this type of element. Default behavior here is to require only a unique name and
	 * label. Override this if customized validation is required.
	 *
	 *     into the field values.
	 *
	 * @param array   $posted_data = $_POST data
	 * @return array filtered field_data that can be saved OR can be safely repopulated
	 */
	public function save_definition_filter($posted_data) {
		$posted_data = parent::save_definition_filter($posted_data);
		if (empty($posted_data['alternate_input']) && empty($posted_data['options'])) {
			$this->errors['options'][] = __('At least one option or alternate input is required.', CCTM_TXTDOMAIN);
		}
		return $posted_data; // filtered data
	}
}


/*EOF*/
