<?php
/**
 * CCTM_multiselect
 *
 * Implements an HTML multi-select element with options (multiple select).
 *
 * @package CCTM_FormElement
 */
 
class CCTM_multiselect extends CCTM_FormElement
{
	public $props = array(
		'label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra'	=> '',
		'default_value' => '',
		'required' => '',
		'options'	=> array(),
		'values'	=> array(), // only used if use_key_values = 1
		'use_key_values' => 0, // if 1, then 'options' will use key => value pairs.
		'output_filter' => 'to_array',
		'display'	=> 'checkboxes', // checkboxes|multiselect
		// 'type'	=> '', // auto-populated: the name of the class, minus the CCTM_ prefix.
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
	* @return	string
	*/
	public function get_name() {
		return __('Multi-select',CCTM_TXTDOMAIN);	
	}
	
	//------------------------------------------------------------------------------
	/**
	* This function gives a description of this type of field so users will know 
	* whether or not they want to add this type of field to their custom content
	* type. The returned value should be localized using the __() function.
	* @return	string text description
	*/
	public function get_description() {
		return __('Multi-select fields implement a <select> element which lets you select mutliple items. "Extra" parameters, e.g. "size" can be specified in the definition.',CCTM_TXTDOMAIN);
	}

	//------------------------------------------------------------------------------
	/**
	* This function should return the URL where users can read more information about
	* the type of field that they want to add to their post_type. The string may
	* be localized using __() if necessary (e.g. for language-specific pages)
	*
	* @return	string 	e.g. http://www.yoursite.com/some/page.html
	*/
	public function get_url() {
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/MultiSelect';
	}
	
	//------------------------------------------------------------------------------
	/**
	 * This is the odd duck... it could (should?) be implemented as a variation of 
	 * the dropbox field (it is so similar), but conceptually, the multi-select is a
	 * different animal.  It doesn't get "repeated" like the other fields, instead
	 * it _always_ stores an array of values.  Notably, this field uses only the 
	 * option and wrapper .tpl's.
	 *
	 * This is hands-down the most complex field due to the way we have to do 
	 * literal comparisions of foreign comparisons.  Whereas the other fields 
	 * are fine if we store a "&agrave;" or "ˆ" so long as it displays correctly,
	 * the multiselect fields must get the $current_value to be EXACTLY equal
	 * to the available options, otherwise we won't know whether or not 
	 * to check the checkbox.
	 *
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=88
	 *
	 * @param string $current_value json-encoded array of selected values for the current post
	 * @return string
	 */
	public function get_edit_field_instance($current_value) {

		$this->id      = str_replace(array('[',']',' '), '_', $this->name);

		$optiontpl = '';
		$fieldtpl = '';
		$wrappertpl = '';

		// Multi-select
		if (isset($this->display) && $this->display == 'multiselect') {

			$optiontpl = CCTM::load_tpl(
				array('fields/options/'.$this->name.'.tpl'
					, 'fields/options/_option.tpl'
				)
			);		

			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_multiselect.tpl'
				)
			);

		} 
		// Multi-checkboxes
		else {
		
			$optiontpl = CCTM::load_tpl(
				array('fields/options/'.$this->name.'.tpl'
					, 'fields/options/_'.$this->type.'.tpl'
					, 'fields/options/_checkbox.tpl'
				)
			);
			
			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_multi_checkboxes.tpl'
					, 'fields/wrappers/_default.tpl'
				)
			);
		}
		
		// See https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=463
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
        
        // $current_values_arr: represents what's actually been selected.
		//$current_values_arr = (array) json_decode(html_entity_decode($current_value), true );
		$current_values_arr = $this->get_value(html_entity_decode($current_value), 'to_array');
	
		// Bring the foreign characters back from the dead.  We need this extra step 
		// because we have to do exact comparisons to see if the options are selected or not.
		if ( $current_values_arr and is_array($current_values_arr) ) {
			foreach ( $current_values_arr as $i => $v ) {
				$current_values_arr[$i] = trim(CCTM::charset_decode_utf_8($v));
			}
		}

		// $this->options: represents what's _available_ to be selected.
		// Some error messaging: the options thing is enforced at time of def creation too,
		// but we're doing it here too just to play it safe.
		if ( !isset($this->options) || !is_array($this->options) ) {
			return sprintf('<p><strong>%$1s</strong> %$2s %$3s</p>'
				, __('Custom Content Error', CCTM_TXTDOMAIN)
				, __('No options supplied for the following custom field: ', CCTM_TXTDOMAIN)
				, $this->name
			);
		}

		// Get the options!
		// we use a for loop so we can read places out of 2 similar arrays: values & options
		$opt_cnt = count($this->options);
		for ( $i = 0; $i < $opt_cnt; $i++ ) {
			
			// initialize
			$hash = $this->get_props();
			$hash['is_checked'] = '';
			$hash['is_selected'] = '';
			$hash['option'] = '';
			$hash['value'] = '';
			$hash['i'] = $i;
			
			if (isset($this->options[$i])) {
				$hash['option'] = CCTM::charset_decode_utf_8($this->options[$i]);
			}
			
			if (isset($this->values[$i])) {
				$hash['value'] = CCTM::charset_decode_utf_8($this->values[$i]);
			}
			// Simplistic behavior if we don't use key=>value pairs
			if ( !$this->use_key_values ) {
				$hash['value'] = $hash['option'];
			}

			if ( is_array($current_values_arr) && in_array( trim($hash['value']), $current_values_arr) ) {
				$hash['is_checked'] = 'checked="checked"';
				$hash['is_selected'] = 'selected="selected"';
				
			}
		
			$this->content .= CCTM::parse($optiontpl, $hash);
		}
		$this->set_prop('value', $current_value);
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
	 * @param mixed $def	nested array of existing definition.
	 */
	public function get_edit_field_definition($def) {
	
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
			
		// Standard
		$out = $this->format_standard_fields($def, false);

		// Options		
		$out .= '
			<div class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span>'. __('Options', CCTM_TXTDOMAIN).'</span></h3>
				<div class="inside">
					<table><tr><td width="600" style="vertical-align:top">';

		// Use Key => Value Pairs?  (if not, the simple usage is simple options)
		$out .= '<input type="hidden" name="use_key_values" value="0"/>
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
		$hash['option_cnt'] 	= $option_cnt;
		$hash['delete'] 		= __('Delete');
		$hash['options'] 		= __('Options', CCTM_TXTDOMAIN);
		$hash['values']			= __('Stored Values', CCTM_TXTDOMAIN);
		$hash['add_option'] 	= __('Add Option',CCTM_TXTDOMAIN);
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
		
		// this html should match up with the js html in manager.js
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
					$option_txt = CCTM::charset_decode_utf_8(trim($def['options'][$i]));
				}
				$value_txt = '';
				if (isset($def['values'][$i])) {
					$value_txt = CCTM::charset_decode_utf_8(trim($def['values'][$i]));
				}
				
				$option_css_id = 'cctm_dropdown_option'.$opt_i;
				$out .= sprintf($option_html
					, $option_css_id
					, $opt_i
					, $option_txt
					, $opt_i
					, $value_txt
					, $option_css_id, __('Delete') 
					, $opt_i
					, __('Set as Default') 
				);
				$opt_i = $opt_i + 1;
			}
		}
			
		$out .= '
			</tbody>
		</table>'; // close id="dropdown_options" 
		
		// Display: multi-select or as multiple checkboxes
		$checkboxes_is_selected = '';
		$multiselect_is_selected = '';
		if (isset($def['display']) && $def['display'] == 'checkboxes') {
			$checkboxes_is_selected = ' selected="selected"';
		}
		elseif (isset($def['display']) && $def['display'] == 'multiselect') {
			$multiselect_is_selected = ' selected="selected"';
		}
		$out .= '<div class="'.self::wrapper_css_class .'" id="display_wrapper">
				 <label for="display" class="cctm_label" id="display_label">'
			. __('Display', CCTM_TXTDOMAIN) .
			'</label>
				 <select name="display" id="display">
				 	<option value="checkboxes" '.$checkboxes_is_selected.'>'. __('Checkboxes', CCTM_TXTDOMAIN) .'</option>
				 	<option value="multiselect" '.$multiselect_is_selected.'>'. __('Multi-select', CCTM_TXTDOMAIN) .'</option>
				 </select>
				<span class="cctm_description">'.__('Multiple options can be selected either as a series of checkboxes or as a multi-select field.', CCTM_TXTDOMAIN).'</span>
			 	</div>';		

		// Secondary Input options
		$out .= '</td><td style="vertical-align:top">
			<label class="cctm_label cctm_textarea_label" id="advanced_label">'
			. __('Alternate Input', CCTM_TXTDOMAIN) .
			'</label>
			<span>'.__('Use this input if you want to add options in bulk. 
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
	 * this type of element. Default behavior here is require only a unique name and 
	 * label. Override this if customized validation is required.
	 *
	 * @param	array	$posted_data = $_POST data
	 * @return	array	filtered field_data that can be saved OR can be safely repopulated
	 *					into the field values.
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
