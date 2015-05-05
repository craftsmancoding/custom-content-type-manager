<?php
/**
 * CCTM_textarea
 *
 * Implements an HTML textarea input.
 *
 * @package CCTM_FormElement
 */


class CCTM_textarea extends CCTM_FormElement
{

	public $props = array(
		'label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra' => '',
		'default_value' => '',
		'required' => '',
		// 'type' => '', // auto-populated: the name of the class, minus the CCTM_ prefix.
		// 'sort_param' => '', // handled automatically
	);

	//------------------------------------------------------------------------------
	/**
	 * This function provides a name for this type of field. This should return plain
	 * text (no HTML). The returned value should be localized using the __() function.
	 *
	 * @return string
	 */
	public function get_name() {
		return __('Textarea', CCTM_TXTDOMAIN);
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
		return __('Textarea fields implement the standard <textarea> element. "Extra" parameters, e.g. "cols" can be specified in the definition.', CCTM_TXTDOMAIN);
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
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Textarea';
	}


	//------------------------------------------------------------------------------
	/**
	 * <label for="[+name+]" class="cctm_label cctm_textarea_label" id="cctm_label_[+name+]">[+label+]</label>
	 * <textarea name="[+name+]" class="cctm_textarea" id="[+name+]" [+extra+]>[+value+]</textarea>
	 *
	 * @param string  $current_value current value for this field.
	 * @return string
	 */
	public function get_edit_field_instance($current_value) {

		$this->id      = str_replace(array('[',']',' '), '_', $this->name);


		$fieldtpl = '';
		$wrappertpl = '';

		// Multi-versions
		if ($this->is_repeatable) {
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_'.$this->type.'_multi.tpl'
				)
			);

			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_'.$this->type.'_multi.tpl'
				)
			);

			$this->i = 0;

			$values = $this->get_value($current_value,'to_array');

			foreach ($values as $v) {
				$this->value = htmlspecialchars( html_entity_decode($v), ENT_QUOTES);
				$this->content .= CCTM::parse($fieldtpl, $this->get_props());
				$this->i   = $this->i + 1;
			}
		}
		// Simple stuff
		else {
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_'.$this->type.'.tpl'
					, 'fields/elements/_default.tpl'
				)
			);

			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_'.$this->type.'.tpl'
					, 'fields/wrappers/_default.tpl'
				)
			);
			
			$this->value = htmlspecialchars($this->get_value($current_value,'to_string'), ENT_QUOTES);
			$this->content = CCTM::parse($fieldtpl, $this->get_props());
		}

		// wrap it.
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

		// Standard
		$out = $this->format_standard_fields($def);
		
		// Validations / Required
		$out .= $this->format_validators($def);

		// Output Filter
		$out .= $this->format_available_output_filters($def);
			 	
		return $out;
	}


}


/*EOF*/