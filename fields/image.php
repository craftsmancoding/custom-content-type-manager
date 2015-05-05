<?php
/**
 * CCTM_image
 *
 * Implements an field that stores a reference to an image (i.e. an attachment post that is an image)
 *
 * @package CCTM_FormElement
 */


class CCTM_image extends CCTM_FormElement
{
	public $props = array(
		'label' => '',
		'button_label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra' => '',
		'default_value' => '',
		'is_repeatable' => '',
		'required' => '',
		'search_parameters' => '',
		'output_filter' => 'to_image_src',
		// 'type' => '', // auto-populated: the name of the class, minus the CCTM_ prefix.
	);

	//------------------------------------------------------------------------------
	/**
	 * Thickbox support
	 */
	public function admin_init($fieldlist=array()) {
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_register_script('cctm_relation', CCTM_URL.'/js/relation.js', array('jquery', 'media-upload', 'thickbox'));
		wp_enqueue_script('cctm_relation');
	}


	//------------------------------------------------------------------------------
	/**
	 * Get the standard fields
	 *
	 * @param	array	current def
	 * @return	strin	HTML
	 */
	public function format_standard_fields($def, $show_repeatable=true) {
		$is_checked = '';
		if (isset($def['is_repeatable']) && $def['is_repeatable'] == 1) {
			$is_checked = 'checked="checked"';
		}

		$out = '<div class="postbox">
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class="hndle"><span>'. __('Standard Fields', CCTM_TXTDOMAIN).'</span></h3>
			<div class="inside">';
			
		// Label
		$out .= '<div class="'.self::wrapper_css_class .'" id="label_wrapper">
			 		<label for="label" class="'.self::label_css_class.'">'
			.__('Label', CCTM_TXTDOMAIN).'</label>
			 		<input type="text" name="label" class="'.self::css_class_prefix.'text" id="label" value="'.htmlspecialchars($def['label']) .'"/>
			 		' . $this->get_translation('label').'
			 	</div>';
		// Name
		$out .= '<div class="'.self::wrapper_css_class .'" id="name_wrapper">
				 <label for="name" class="cctm_label cctm_text_label" id="name_label">'
			. __('Name', CCTM_TXTDOMAIN) .
			'</label>
				 <input type="text" name="name" class="cctm_text" id="name" value="'.htmlspecialchars($def['name']) .'"/>'
			. $this->get_translation('name') .'
			 	</div>';

		// Default Value
		$out .= '<div class="'.self::wrapper_css_class .'" id="default_value_wrapper">
			 	<label for="default_value" class="cctm_label cctm_text_label" id="default_value_label">'
			.__('Default Value', CCTM_TXTDOMAIN) .'</label>
			 		<input type="text" name="default_value" class="cctm_text" id="default_value" value="'. htmlspecialchars($def['default_value'])
			.'"/>
			 	' . $this->get_translation('default_value') .'
			 	</div>';

		// Extra
		$out .= '<div class="'.self::wrapper_css_class .'" id="extra_wrapper">
			 		<label for="extra" class="'.self::label_css_class.'">'
			.__('Extra', CCTM_TXTDOMAIN) .'</label>
			 		<input type="text" name="extra" class="cctm_text" id="extra" value="'
			.htmlspecialchars($def['extra']).'"/>
			 	' . $this->get_translation('extra').'
			 	</div>';

		// Class
		$out .= '<div class="'.self::wrapper_css_class .'" id="class_wrapper">
			 	<label for="class" class="'.self::label_css_class.'">'
			.__('Class', CCTM_TXTDOMAIN) .'</label>
			 		<input type="text" name="class" class="cctm_text" id="class" value="'
			.htmlspecialchars($def['class']).'"/>
			 	' . $this->get_translation('class').'
			 	</div>';

		if ($show_repeatable) {
			// Is Repeatable?
			$out .= '<div class="'.self::wrapper_css_class .'" id="is_repeatable_wrapper">
					 <label for="is_repeatable" class="cctm_label cctm_checkbox_label" id="is_repeatable_label">'
				. __('Is Repeatable?', CCTM_TXTDOMAIN) .
				'</label>
					 <br />
					 <input type="checkbox" name="is_repeatable" class="cctm_checkbox" id="is_repeatable" value="1" '. $is_checked.'/> <span>'.$this->descriptions['is_repeatable'].'</span>
				 	</div>';
		}

		// Description
		$out .= '<div class="'.self::wrapper_css_class .'" id="description_wrapper">
			 	<label for="description" class="'.self::label_css_class.'">'
			.__('Description', CCTM_TXTDOMAIN) .'</label>
			 	<textarea name="description" class="cctm_textarea" id="description" rows="5" cols="60">'. htmlspecialchars($def['description']).'</textarea>
			 	' . $this->get_translation('description').'
			 	</div>';
			 	
		$out .= '</div><!-- /inside -->
			</div><!-- /postbox -->';	 	
		
		return $out;	
	}


	//------------------------------------------------------------------------------
	/**
	 * This function provides a name for this type of field. This should return plain
	 * text (no HTML). The returned value should be localized using the __() function.
	 *
	 * @return string
	 */
	public function get_name() {
		return __('Image', CCTM_TXTDOMAIN);
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
		return __('Image fields are used to store references to any image that has been uploaded via the WordPress media uploader.', CCTM_TXTDOMAIN);
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
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Image';
	}


	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @param mixed   $current_value current value for this field (an integer ID).
	 * @return string
	 */
	public function get_edit_field_instance($current_value) {

		require_once CCTM_PATH.'/includes/GetPostsQuery.php';

		$Q = new GetPostsQuery();

		// Populate the values (i.e. properties) of this field
		$this->id      = str_replace(array('[',']',' '), '_', $this->name);
		$this->content   = '';
		$this->post_id   = $this->value;

		$fieldtpl = '';
		$wrappertpl = '';

		// Multi field?
		if ($this->is_repeatable) {

			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_image_multi.tpl'
					, 'fields/elements/_relation_multi.tpl'
				)
			);

			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_image_multi.tpl'
					, 'fields/wrappers/_relation_multi.tpl'
				)
			);

			$values = $this->get_value($current_value,'to_array');
			foreach ($values as $v) {
				$hash 					= $this->get_props();
				$hash['post_id']    	= (int) $v;
				$hash['thumbnail_url']	= CCTM::get_thumbnail($hash['post_id']);

				// Look up all the data on that foriegn key
				// We gotta watch out: what if the related post has custom fields like "description" or 
				// anything that would conflict with the definition?
				$post =  $Q->get_post($hash['post_id']);
				if (empty($post)) {
					$this->content = '<div class="cctm_error"><p>'.sprintf(__('Image %s not found.', CCTM_TXTDOMAIN), $this->post_id).'</p></div>';
				}	
				else {
					foreach($post as $k => $v) {
						// Don't override the def's attributes!
						if (!isset($hash[$k])) {
							$hash[$k] = $v;
						}
					}
					
					$this->content .= CCTM::parse($fieldtpl, $hash);					
				}
			}

		}
		// Regular old Single-selection
		else {
			$this->post_id    = $this->get_value($current_value,'to_string'); 
			$this->thumbnail_url = CCTM::get_thumbnail($this->post_id);
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_image.tpl'
					, 'fields/elements/_relation.tpl'
				)
			);

			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_image.tpl'
					, 'fields/wrappers/_relation.tpl'
				)
			);

			if ($this->post_id) {
				// Look up all the data on that foriegn key
				// We gotta watch out: what if the related post has custom fields like "description" or 
				// anything that would conflict with the definition?

				$post = $Q->get_post($this->post_id);
				
				if (empty($post)) {
					$this->content = '<div class="cctm_error"><p>'.sprintf(__('Image %s not found.', CCTM_TXTDOMAIN), $this->post_id).'</p></div>';
				}
				else {
					foreach($post as $k => $v) {
						// Don't override the def's attributes!
						if (!isset($this->$k)) {
							$this->$k = $v;
						}
					}
					$this->content = CCTM::parse($fieldtpl, $this->get_props());				
				}
			}
		}

		if (empty($this->button_label)) {
			$this->button_label = __('Choose Image', CCTM_TXTDOMAIN);
		}

		return CCTM::parse($wrappertpl, $this->get_props());
	}


	//------------------------------------------------------------------------------
	/**
	 * This should return (not print) form elements that handle all the controls required to define this
	 * type of field.  The default properties correspond to this class's public variables,
	 * e.g. name, label, etc. The form elements you create should have names that correspond
	 * with the public $props variable. A populated array of $props will be stored alongside
	 * the custom-field data for the containing post-type.
	 *
	 * @param unknown $def
	 * @return string HTML input fields
	 */
	public function get_edit_field_definition($def) {

		// Used to fetch the default value.
		require_once CCTM_PATH.'/includes/GetPostsQuery.php';

		// Standard
		$out = $this->format_standard_fields($def);

		// Options
		$Q = new GetPostsQuery();
		
		$out .= '
			<div class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span>'. __('Options', CCTM_TXTDOMAIN).'</span></h3>
				<div class="inside">';
		
		// Note fieldtype: used to set the default value on new fields
		$out .= '<input type="hidden" id="fieldtype" value="image" />';

		// Initialize / defaults
		$preview_html = '';
		$click_label = __('Choose Image');
		$label = __('Default Value', CCTM_TXTDOMAIN);
		$remove_label = __('Remove');


		// Handle the display of the default value
		if ( !empty($def['default_value']) ) {

			$hash = CCTM::get_thumbnail($def['default_value']);

			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_'.$this->type.'.tpl'
					, 'fields/elements/_relation.tpl'
				)
			);
			$preview_html = CCTM::parse($fieldtpl, $hash);
		}

		// Button Label
		$out .= '<div class="'.self::wrapper_css_class .'" id="button_label_wrapper">
			 		<label for="button_label" class="'.self::label_css_class.'">'
			.__('Button Label', CCTM_TXTDOMAIN).'</label>
			 		<input type="text" name="button_label" class="'.self::css_class_prefix.'text" id="button_label" value="'.htmlspecialchars($def['button_label']) .'"/>
			 		' . $this->get_translation('button_label').'
			 	</div>';

		// Set Search Parameters
		$search_parameters_str = CCTM::get_value($def, 'search_parameters');
		parse_str($search_parameters_str, $args);
		$Q = new GetPostsQuery($args);
		$search_parameters_visible = $Q->get_args();

		$out .= '
			<div class="cctm_element_wrapper" id="search_parameters_wrapper">
				<label for="name" class="cctm_label cctm_text_label" id="search_parameters_label">'
			. __('Search Parameters', CCTM_TXTDOMAIN) .
			'</label>
				<span class="cctm_description">'.__('Define which posts are available for selection by narrowing your search parameters.', CCTM_TXTDOMAIN).'</span>
				<br/>
				<span class="button" onclick="javascript:search_form_display(\''.$def['name'].'\',\''.$def['type'].'\');">'.__('Set Search Parameters', CCTM_TXTDOMAIN) .'</span>
				<div id="cctm_thickbox"></div>
				<span id="search_parameters_visible">'.
				$search_parameters_visible
				.'</span>
				<input type="hidden" id="search_parameters" name="search_parameters" value="'.CCTM::get_value($def, 'search_parameters').'" />
				<br/>
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
     * Options here are any search criteria
     */
    public function get_options_desc() {
        return $this->_get_search_parameters_visible($this->props['search_parameters']);
    }

}


/*EOF*/