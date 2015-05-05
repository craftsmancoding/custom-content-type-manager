<?php
/**
 * CCTM_directory
 *
 * Lists the contents of a directory (an optionally all sub-dirs) for selection in a dropdown.
 *
 * @package CCTM_FormElement
 */
class CCTM_directory extends CCTM_FormElement
{
	public $props = array(
		'label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra' => '',
		'default_value' => '',
		'required' => '',
		'source_dir' => '',
		'pattern' => '',
		'traverse_dirs' => 0,
		// 'type' => '', // auto-populated: the name of the class, minus the CCTM_ prefix.

	);


	//------------------------------------------------------------------------------
	/**
	 * This function provides a name for this type of field. This should return plain
	 * text (no HTML). The returned value should be localized using the __() function.
	 *
	 * @return string
	 */
	public function get_name() {
		return __('Directory', CCTM_TXTDOMAIN);
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
		return __('Lists the contents of a directory (an optionally all sub-dirs) for selection in a dropdown. Output is relative to the defined source directory.', CCTM_TXTDOMAIN);
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
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Directory';
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

		// Format for multi-select
		if ($this->is_repeatable) {
			$current_value = $this->get_value($current_value, 'to_array');
			$optiontpl = CCTM::load_tpl(
				array('fields/options/'.$this->name.'.tpl'
					, 'fields/options/_option.tpl'
				)
			);
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_multiselect.tpl'
					, 'fields/elements/_default.tpl'
				)
			);
			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_multiselect.tpl'
					, 'fields/wrappers/_default.tpl'
				)
			);
		}
		// For regular dropdowns
		else {
			$current_value = $this->get_value($current_value, 'to_string');

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
					, 'fields/wrappers/_default.tpl'
				)
			);
		}


		// Get the options.  This currently is not skinnable.
		$this->all_options = '';

		if (!isset($this->required) || !$this->required) {
			$hash['value'] = '';
			$hash['option'] = '';
			$this->all_options .= CCTM::parse($optiontpl, $hash); // '<option value="">'.__('Pick One').'</option>';
		}

		// Substitutions
		$this->source_dir = str_replace('[+ABSPATH+]', ABSPATH, $this->source_dir);
		$this->source_dir = preg_replace('#/$#','',$this->source_dir); // strip trailing slash
		
		// Generate the regex pattern
		$exts = explode(',',$this->pattern);
		$exts = array_map('trim', $exts);
		$exts = array_map('preg_quote', $exts);
		$pattern = implode('|',$exts);
		
		
		// Get the files
		$options = array();
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->source_dir)) as $filename) {
			if (preg_match('/('.$pattern.')$/i',$filename)) {
				// Make the stored file relative to the source_dir
				$options[] = preg_replace('#^'.$this->source_dir.'/#','',$filename);
			}   
		}
		// See https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=504
		sort($options,SORT_STRING);
		$this->options = $options;
		$opt_cnt = count($this->options);

		$i = 1;
		// Populate the options
		foreach ( $this->options as $o ) {
			//die(print_r($o, true));
			$hash = $this->get_props();
			$hash['option'] = $o;
			$hash['value'] = $o;
			$hash['is_checked'] = '';

			if ($this->is_repeatable) {
				if ( in_array(trim($hash['value']), $current_value )) {
					$hash['is_selected'] = 'selected="selected"';
				}
			}
			else {
				if ( trim($current_value) == trim($hash['value']) ) {
					$hash['is_selected'] = 'selected="selected"';
				}
			}

			$hash['i'] = $i;
			$hash['id'] = $this->name;

			$this->all_options .= CCTM::parse($optiontpl, $hash);
		}



		// Populate the values (i.e. properties) of this field
		$this->id      = str_replace(array('[',']',' '), '_', $this->name);

		// wrap
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
		$out = $this->format_standard_fields($def);
		
		// Options
		$is_checked = '';
		if (isset($def['traverse_dirs']) && $def['traverse_dirs']==1) {
			$is_checked = 'checked="checked"';
		}
		
		// Source Directory
		$out .= '
			<div class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span>'. __('Options', CCTM_TXTDOMAIN).'</span></h3>
				<div class="inside">';
				
		$out .= '<div class="'.self::wrapper_css_class .'" id="source_dir_wrapper">
				 <label for="source_dir" class="cctm_label cctm_text_label" id="source_dir_label">'
			. __('Source Directory', CCTM_TXTDOMAIN) .
			'</label>
				 <input type="text" name="source_dir" class="cctm_text_short" size="50" id="source_dir" value="'.htmlspecialchars($def['source_dir']) .'"/><span class="cctm_description">'.
				 __('Full path to a directory without the trailing slash, e.g. <code>/home/my_user/dir</code>. Use <code>[+ABSPATH+]</code> as a placeholder for your WordPress root directory.',CCTM_TXTDOMAIN).'</span>
			 	</div>';

		// pattern
		$out .= '<div class="'.self::wrapper_css_class .'" id="pattern_wrapper">
				 <label for="pattern" class="cctm_label cctm_text_label" id="pattern_label">'
			. __('Extensions', CCTM_TXTDOMAIN) .
			'</label>
				 <input type="text" name="pattern" class="cctm_text_short" id="pattern" value="'.htmlspecialchars($def['pattern']) .'"/> <span class="cctm_description">'
			. __('File extensions you want returned. Use comas to separate possible matches, e.g. <code>.jpg,.jpeg</code>.  The case does not matter.',CCTM_TXTDOMAIN) .'</span>
			 	</div>';
		// Traverse Directories?
		$out .= '<div class="'.self::wrapper_css_class .'" id="traverse_dirs_wrapper">
				 <label for="traverse_dirs" class="cctm_label cctm_checkbox_label" id="traverse_dirs_label">'
			. __('Traverse Directories?', CCTM_TXTDOMAIN) .
			'</label>
				 <br />
				 <input type="checkbox" name="traverse_dirs" class="cctm_checkbox" id="traverse_dirs" value="1" '. $is_checked.'/> <span>'.__('When checked, the contents of sub-directories will also be listed.',CCTM_TXTDOMAIN).'</span>
			 	</div>';
			 	
		$out .= '</div><!-- /inside -->
			</div><!-- /postbox -->';

		// Validations / Required
		$out .= $this->format_validators($def,false);
		
		// Output Filter
		$out .= $this->format_available_output_filters($def);

		return $out;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Shows the selected directory and extensions
	 */
    public function get_options_desc() {
        $out = '';
        if (!empty($this->props['source_dir'])) {
            $out .= __('Source Directory', CCTM_TXTDOMAIN) . ': '. $this->props['source_dir'] .'<br/>';
            $out .= __('Extensions', CCTM_TXTDOMAIN) . ': '. $this->props['pattern'] .'<br/>';
            $is_checked = '';
    		if (isset($def['traverse_dirs']) && $def['traverse_dirs']==1) {
    			$is_checked = 'checked="checked"';
    		}
            $out .= __('Traverse Directories?', CCTM_TXTDOMAIN) . sprintf(' <input type="checkbox" disabled="disabled" %s/>',$is_checked);
        }
        return $out;
    }	
}


/*EOF*/