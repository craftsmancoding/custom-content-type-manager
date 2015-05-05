<?php
/**
 * CCTM_wysiwyg
 *
 * Implements an WYSIWYG textarea input (a textarea with formatting controls).
 *
 * @package CCTM_FormElement
 */


class CCTM_wysiwyg extends CCTM_FormElement
{
	public $props = array(
		'label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra' => 'cols="80" rows="10"',
		'default_value' => '',
		'required' => '',
		'output_filter' => 'do_shortcode',
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
		return __('WYSIWYG', CCTM_TXTDOMAIN);
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
		return __('What-you-see-is-what-you-get (WYSIWYG) fields implement a <textarea> element with formatting controls. "Extra" parameters, e.g. "cols" can be specified in the definition, however a minimum size is required to make room for the formatting controls.', CCTM_TXTDOMAIN);
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
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/WYSIWYG';
	}


	//------------------------------------------------------------------------------
	/**
	 * See Issue http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=138
	 * and this one: http://keighl.com/2010/04/switching-visualhtml-modes-with-tinymce/
	 *
	 * @param string  $current_value current value for this field.
	 * @return string
	 */
	public function get_edit_field_instance($current_value) {

		$this->id      = str_replace(array('[',']',' '), '_', $this->name);

		$wrappertpl = CCTM::load_tpl(
			array('fields/wrappers/'.$this->name.'.tpl'
				, 'fields/wrappers/_'.$this->type.'.tpl'
				, 'fields/wrappers/_default.tpl'
			)
		);
		
		$settings = array();
		$settings['editor_class'] = $this->class;
		$settings['textarea_name'] = $this->name_prefix.$this->name;

		// see http://nacin.com/tag/wp_editor/
		ob_start();
		wp_editor($current_value, $this->id_prefix.$this->id, $settings);
		$this->content = ob_get_clean();

		$this->add_label = __('Add', CCTM_TXTDOMAIN);

		return CCTM::parse($wrappertpl, $this->get_props());
	}


	//------------------------------------------------------------------------------
	/**
	 * @param array   $def field definition; see the $props array
	 * @return string
	 */
	public function get_edit_field_definition($def) {

		// Standard
		$out = $this->format_standard_fields($def, false);

		// Validations / Required
		$out .= $this->format_validators($def);
		
		// Output Filter
		$out .= $this->format_available_output_filters($def);
			 	
		return $out;
	}

	//------------------------------------------------------------------------------
	/**
	 * Custom filter on the name due to WP's limitations:
	 * http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=271
	 */
	public function save_definition_filter($posted_data) {
	
		$posted_data = parent::save_definition_filter($posted_data);
		
		// Are there any invalid characters? 1st char. must be a letter (req'd for valid prop/func names)
		if ( !empty($posted_data['name']) && !preg_match('/^[a-z]*$/', $posted_data['name'])) {
			$this->errors['name'][] = 
				__('Due to WordPress limitations, WYSIWYG fields can contain ONLY lowercase letters.', CCTM_TXTDOMAIN);
			$posted_data['name'] = preg_replace('/[^a-z]/', '', $posted_data['name']);
		}
		
		return $posted_data;	
	}
}


/*EOF*/