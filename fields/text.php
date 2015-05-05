<?php
/**
 * CCTM_text
 *
 * Implements a simple HTML text input.
 *
 * @package CCTM_FormElement
 */


class CCTM_text extends CCTM_FormElement
{

	public $props = array(
		'label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra' => '',
		'default_value' => '',
		'is_repeatable' => '',
		'output_filter' => '',
		'required' => '',
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
		return __('Text', CCTM_TXTDOMAIN);
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
		return __('Text fields implement the standard <input="text"> element. "Extra" parameters, e.g. "size" can be specified in the definition.', CCTM_TXTDOMAIN);
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
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Text';
	}


	//------------------------------------------------------------------------------
	/**
	 * Edit a single instance of the field (e.g. on a post or page's "edit" page).
	 * This is somewhat tricky if the values the user wants to store are HTML/JS.
	 * See http://www.php.net/manual/en/function.htmlspecialchars.php#99185
	 *
	 * @param mixed   $current_value current value for this field.
	 * @return string
	 */
	public function get_edit_field_instance($current_value) {

		// Populate the values (i.e. properties) of this field
		$this->id      = str_replace(array('[',']',' '), '_', $this->name);
		$fieldtpl = '';
		$wrappertpl = '';

		if ($this->is_repeatable) {
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_'.$this->type.'_multi.tpl'
					, 'fields/elements/_default.tpl'
				)
			);
			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_'.$this->type.'_multi.tpl'
					, 'fields/wrappers/_default.tpl'
				)
			);

			$this->i = 0;
			$values = $this->get_value($current_value,'to_array');
			$content = '';
			foreach ($values as $v) {
				$this->value = htmlspecialchars( html_entity_decode($v) );
				$this->content .= CCTM::parse($fieldtpl, $this->get_props());
				$this->i   = $this->i + 1;
			}
		}
		// Normal text field
		else {
			$this->value  = htmlspecialchars( html_entity_decode($this->get_value($current_value,'to_string') ));

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
			$this->content = CCTM::parse($fieldtpl, $this->get_props());
		}


		$this->add_label = __('Add', CCTM_TXTDOMAIN);
		return CCTM::parse($wrappertpl, $this->get_props());
	}


	//------------------------------------------------------------------------------
	/**
	 * This generates the HTML used when editing the field's definition.
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

    //------------------------------------------------------------------------------
    /**
     * Show a bit about validation rules if we can
     * @return string
     */
    public function get_options_desc() {        
        $out = '';
        if (!empty($this->props['validator'])) {
            $Vobj = CCTM::load_object($this->props['validator'], 'validators');
				if ($Vobj) {
                    $out .= $Vobj->get_name() .'<br/>';
				}
        }
        if ($this->props['required']) {
            $out .= sprintf('<span style="color:red;">%s</span><br/>',__('Required', CCTM_TXTDOMAIN));
        }
        if (!empty($this->props['default_value'])) {
            $out .= ' '.$this->props['default_value'] .'<em>('.__('default',CCTM_TXTDOMAIN).')</em>';
        }
        return $out;
    }

}


/*EOF*/