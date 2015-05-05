<?php
/**
 * CCTM_hidden
 *
 * Implements a hidden field input.  This is useful when you want to programmatically 
 * edit a value on the form instead of relying on the user to edit it.
 * Hidden fields are not repeatable, and they do not use a wrapper tpl (no point, really)
 *
 * @package CCTM_FormElement
 */


class CCTM_hidden extends CCTM_FormElement {

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
		'evaluate_create_value' => 0,
		'evaluate_update_value' => 0,
		'evaluate_onsave' => 0,
		'create_value_code' => '',
		'update_value_code' => '',
		'onsave_code' => '',
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
		return __('Hidden', CCTM_TXTDOMAIN);
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
		return __('Hidden fields implement the standard <input="hidden"> element. They can be used to store data programmatically, out of view from users.', CCTM_TXTDOMAIN);
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
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Hidden';
	}

	//------------------------------------------------------------------------------
	/**
	 * This is somewhat tricky if the values the user wants to store are HTML/JS.
	 * See http://www.php.net/manual/en/function.htmlspecialchars.php#99185
	 *
	 * @param mixed   $current_value current value for this field.
	 * @return string
	 */
	public function get_create_field_instance() {

		// Populate the values (i.e. properties) of this field
		$this->id      = str_replace(array('[',']',' '), '_', $this->name);

		$fieldtpl = '';
		$wrappertpl = '';

		if ($this->evaluate_create_value) {
			$this->value = eval($this->create_value_code);
		}
		else {
			$this->value  = $this->default_value;
		}

		$fieldtpl = CCTM::load_tpl(
			array('fields/elements/'.$this->name.'.tpl'
				, 'fields/elements/_hidden.tpl'
				, 'fields/elements/_default.tpl'
			)
		);

		return CCTM::parse($fieldtpl, $this->get_props()) 
			. '<input type="hidden" name="_cctm_is_create" value="1" />';
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

		// Populate the values (i.e. properties) of this field
		$this->id      = $this->name;

		$fieldtpl = '';
		$wrappertpl = '';

		if ($this->evaluate_update_value) {
			$this->value = eval($this->update_value_code);
		}
		else {
			$this->value  = htmlspecialchars( html_entity_decode($this->get_value($current_value,'to_string') ));
		}
		

		$fieldtpl = CCTM::load_tpl(
			array('fields/elements/'.$this->name.'.tpl'
				, 'fields/elements/_hidden.tpl'
				, 'fields/elements/_default.tpl'
			)
		);

		return CCTM::parse($fieldtpl, $this->get_props());
	}


	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @param mixed   $def field definition; see the $props array
	 * @return string
	 */
	public function get_edit_field_definition($def) {
	   // eval update/create value = euv/ecv
		$is_ecv_checked = '';
		$is_euv_checked = '';
		$is_onsave_checked = '';
		
		if (isset($def['evaluate_create_value']) && $def['evaluate_create_value'] == 1) {
			$is_ecv_checked = 'checked="checked"';
		}
		if (isset($def['evaluate_update_value']) && $def['evaluate_update_value'] == 1) {
			$is_euv_checked = 'checked="checked"';
		}
		if (isset($def['evaluate_onsave']) && $def['evaluate_onsave'] == 1) {
			$is_onsave_checked = 'checked="checked"';
		}
	
		// Standard
		$out = $this->format_standard_fields($def, false);
		
		// Evaluate Default Value (use PHP eval)
		$out .= '
			<div class="postbox">
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class="hndle"><span>'. __('Special', CCTM_TXTDOMAIN).'</span></h3>
			<div class="inside">
			<div class="'.self::wrapper_css_class .'" id="evaluate_default_value_wrapper">
				 <label for="evaluate_default_value" class="cctm_label cctm_checkbox_label" id="evaluate_default_value_label">'
			. __('EXPERIMENTAL USE ONLY. Use PHP eval to calculate values? (Omit the php tags and return a value, e.g. <code>return date(\'Y-m-d\');</code> ).', CCTM_TXTDOMAIN) .
			'</label>
				 <br />
				 <input type="checkbox" name="evaluate_create_value" class="cctm_checkbox" id="evaluate_create_value" value="1" '. $is_ecv_checked.'/> '
			.__('Evaluate "OnCreate". This executes when the form for a new post is drawn.', CCTM_TXTDOMAIN).'<br/>
			
				<input type="checkbox" name="evaluate_update_value" class="cctm_checkbox" id="evaluate_update_value" value="1" '. $is_euv_checked.'/> '
			.__('Evaluate "OnEdit".  This executes when the form for an existing post is drawn.', CCTM_TXTDOMAIN).'<br/>
    			<input type="checkbox" name="evaluate_onsave" class="cctm_checkbox" id="evaluate_onsave" value="1" '. $is_onsave_checked.'/> '
			.__('Evaluate "OnSave". This executes when the post form is submitted.', CCTM_TXTDOMAIN).'
			 </div>
			 
			 <div class="'.self::wrapper_css_class .'" id="evaluate_create_value_wrapper">
			 		<label for="create_value_code" class="cctm_label cctm_textarea_label" id="create_value_code_label">'.__('OnCreate',CCTM_TXTDOMAIN).'</label>

			 		<textarea id="evaluate_create_value" name="create_value_code" rows="5" cols="60">'.$def['create_value_code'].'</textarea>
			 		<label for="evaluate_update_value" class="cctm_label cctm_textarea_label" id="evaluate_update_value_label">'.__('OnEdit', CCTM_TXTDOMAIN).'</label>
			 		<textarea id="evaluate_update_value" name="update_value_code" rows="5" cols="60">'.$def['update_value_code'].'</textarea>
			 		<label for="onsave_code" class="cctm_label cctm_textarea_label" id="onsave_code_label">'.__('OnSave', CCTM_TXTDOMAIN).'</label>
			 		<textarea id="onsave_code" name="onsave_code" rows="5" cols="60">'.$def['onsave_code'].'</textarea>
			 	
			 </div>
			 	
			 	
			 	</div><!-- /inside -->
			</div><!-- /postbox -->';
		
		// Output Filter
		$out .= $this->format_available_output_filters($def);

		return $out;
	}

	//------------------------------------------------------------------------------
	/**
	 * See docs in parent class.  This is here so we can eval code onsave.
	 *
	 * @param mixed   $posted_data $_POST data
	 * @param string  $field_name: the unique name for this instance of the field
	 * @return string whatever value you want to store in the wp_postmeta table where meta_key = $field_name
	 */
	public function save_post_filter($posted_data, $field_name) {
	
		global $wp_version;

		if ( isset($posted_data[ CCTM_FormElement::post_name_prefix . $field_name ]) ) {
//                print_r($_POST);  print 'asdfasdfasdf'; exit;
//                print_r($this->props); exit;
            if ($this->props['evaluate_onsave']) {

                return eval($this->onsave_code);
            }
            else {
                return stripslashes(trim($posted_data[ CCTM_FormElement::post_name_prefix . $field_name ]));
            }
		}
		else {
			return '';
		}
	}

}


/*EOF*/