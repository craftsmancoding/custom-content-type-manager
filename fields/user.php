<?php
/**
 * CCTM_user
 *
 * Allows users to select a user
 *
 * wp_usermeta:
 * wp_user_level
 * 0    subscriber
 * 1    contributor
 * 2    author
 * 7    editor
 * 10    admin
 *
 * @package CCTM_FormElement
 */


class CCTM_user extends CCTM_FormElement {

	public $props = array(
		'label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra' => '',
		'default_value' => '',
		'is_repeatable' => '',
		'required' => '',
		'output_filter' => 'userinfo',
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
		return __('User', CCTM_TXTDOMAIN);
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
		return __('User fields let you select a local user from the site.', CCTM_TXTDOMAIN);
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
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/User';
	}


	//------------------------------------------------------------------------------
	/**
	 * This is somewhat tricky if the values the user wants to store are HTML/JS.
	 * See http://www.php.net/manual/en/function.htmlspecialchars.php#99185
	 *
	 * @param mixed   $current_value current value for this field.
	 * @return string
	 */
	public function get_edit_field_instance($current_value) {

		// Format for multi-select
		if ($this->is_repeatable) {
			$current_value = $this->get_value($current_value, 'to_array');
			$optiontpl = CCTM::load_tpl(
				array('fields/options/'.$this->name.'.tpl'
					, 'fields/options/_user_multi.tpl'
					, 'fields/options/_user.tpl'
				)
			);
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_user_multi.tpl'
					, 'fields/elements/_default.tpl'
				)
			);
			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_user_multi.tpl'
					, 'fields/wrappers/_default.tpl'
				)
			);
		}
		// For regular dropdowns
		else {
			$current_value = $this->get_value($current_value, 'to_string');

			$optiontpl = CCTM::load_tpl(
				array('fields/options/'.$this->name.'.tpl'
					, 'fields/options/_user.tpl'
				)
			);
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_user.tpl'
					, 'fields/elements/_default.tpl'
				)
			);
			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_user.tpl'
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

		$this->options = get_users(); // WP: http://codex.wordpress.org/Function_Reference/get_users
		$opt_cnt = count($this->options);

		$i = 1;
		// Populate the options
		foreach ( $this->options as $o ) {
			//die(print_r($o, true));
			$hash = $this->get_props();

			// We hardcode this one because we always need to store the user ID as the value for normalization
			$hash['value'] = $o->data->ID;

			foreach ($o->data as $k => $v) {
				if (!isset($hash[$k])) {
					$hash[$k] = $v;
				}
			}

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
        $this->set_prop('value', $current_value);
		$this->content = CCTM::parse($fieldtpl, $this->get_props());
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
		$out .= $this->format_validators($def, false);

		// Output Filter
		$out .= $this->format_available_output_filters($def);

		return $out;
	}


}


/*EOF*/