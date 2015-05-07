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

        $upload_dir = wp_upload_dir();
        $file = $upload_dir['basedir'] .'/'.CCTM::base_storage_dir.'/fields/'.$this->id.'/oncreate.php';

		if (file_exists($file)) {
			$this->value = include $file;
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
        // Add this flag so we can distinguish between create and edit events
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

        $upload_dir = wp_upload_dir();
        $file = $upload_dir['basedir'] .'/'.CCTM::base_storage_dir.'/fields/'.$this->id.'/onedit.php';

        if (file_exists($file)) {
            $this->value = include $file;
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

		// Standard
		$out = $this->format_standard_fields($def, false);

		// Evaluate Default Value (use PHP eval)
		$out .= '
			<div class="postbox">
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class="hndle"><span>'. __('Special', CCTM_TXTDOMAIN).'</span></h3>
			<div class="inside">
			<div class="'.self::wrapper_css_class .'" id="evaluate_default_value_wrapper">
				 <p>'
			. __('EXPERIMENTAL USE ONLY. You can add .php files to  <code>wp-content/uploads/cctm/fields/{fieldname}/</code> to dynamically calculate or modify values, e.g. <code>&lt;?php return date("Y-m-d"); ?&gt;</code>. The allowed file names are listed below.', CCTM_TXTDOMAIN) .
			'</p>
				 <ul>
				 <li>
				 '
			.__('<code>oncreate.php</code> This executes when the form for a new post is drawn.', CCTM_TXTDOMAIN).'</li>

				<li>'
			.__('<code>onedit.php</code> This executes when the form for an existing post is drawn.', CCTM_TXTDOMAIN).'</li>
    			<li>'
			.__('<code>onsave.php</code> This executes when the post form is submitted.', CCTM_TXTDOMAIN).'</li>
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

            $upload_dir = wp_upload_dir();
            $file = $upload_dir['basedir'] .'/'.CCTM::base_storage_dir.'/fields/'.$field_name.'/onsave.php';
            if (file_exists($file)) {

                return include $file;
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