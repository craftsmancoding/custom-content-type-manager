<?php
/*------------------------------------------------------------------------------
This plugin standardizes the custom fields for specified content types, e.g.
post, page, and any other custom post-type you register via a plugin.
------------------------------------------------------------------------------*/
class StandardizedCustomFields {
	//------------------------------------------------------------------------------
	//! Private Functions
	//------------------------------------------------------------------------------
	/**
	 * Get custom fields for this content type, optionally filtered by metabox.
	 *
	 * @param string $post_type the name of the post_type, e.g. post, page.
	 * param string $metabox_id the name of the metabox
	 * @return array of custom field names (ids)
	 */
	private static function _get_custom_fields($post_type, $metabox_id=null) {

		$custom_fields = array();
		if (isset(CCTM::$data['post_type_defs'][$post_type]['custom_fields'])) {
			$custom_fields = CCTM::$data['post_type_defs'][$post_type]['custom_fields'];
		}
		if ($metabox_id) {
			$filtered_fields = array();
			foreach ($custom_fields as $field) {
				if (isset(CCTM::$data['post_type_defs'][$post_type]['map_field_metabox'][$field])
					&& CCTM::$data['post_type_defs'][$post_type]['map_field_metabox'][$field] == $metabox_id
				) {
					$filtered_fields[] = $field;
				}
			}
			return $filtered_fields;
		}
		return $custom_fields;
	}

	//------------------------------------------------------------------------------
	/**
	 * This determines if the user is editing an existing post.
	 *
	 * @return boolean
	 */
	private static function _is_existing_post() {
		if ( substr($_SERVER['SCRIPT_NAME'],strrpos($_SERVER['SCRIPT_NAME'],'/')+1) == 'post.php' ) {
			return true;
		}
		else {
			return false;
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * This determines if the user is creating a new post.
	 *
	 * @return boolean
	 */
	 private static function _is_new_post() {
		if ( substr($_SERVER['SCRIPT_NAME'],strrpos($_SERVER['SCRIPT_NAME'],'/')+1) == 'post-new.php' ) {
			return true;
		}
		else {
			return false;
		}
	}


	//------------------------------------------------------------------------------
	//! Public Functions	
	//------------------------------------------------------------------------------
	/**
	* Create the metabox(es) for each post-type. We run a gauntlet here to see if we
	* even need to add the metabox: normally it's only added if custom fields have
	* been assigned to it, but users can force a metabox to be drawn even if no
	* fields have been assigned to it.
	*/
	public static function create_meta_box() {
		$content_types_array = CCTM::get_active_post_types();
		foreach ( $content_types_array as $post_type ) {
			$mb_ids = array();
			if (isset(CCTM::$data['metabox_defs']) && is_array(CCTM::$data['metabox_defs'])) {
				$mb_ids = array_keys(CCTM::$data['metabox_defs']);
			}

			$all_fields = self::_get_custom_fields($post_type);
			foreach ($mb_ids as $m_id) {
				$fields = self::_get_custom_fields($post_type, $m_id);
				$all_fields = array_diff($all_fields,$fields);
				$m = CCTM::$metabox_def; // default
				if (isset(CCTM::$data['metabox_defs'][$m_id])) {
					$m = CCTM::$data['metabox_defs'][$m_id];
				}

				$callback = 'StandardizedCustomFields::print_custom_fields';
				$callback_args = array('metabox_id'=>$m_id,'fields'=>$fields);
				if (isset($m['callback']) && !empty($m['callback'])) {
					$callback = $m['callback'];
					if (isset($m['callback_args']) && !empty($m['callback_args'])) {
						$callback_args = $m['callback_args'];
					}
				}
				// skip drawing this metabox unless the user has specifically asked for it to be drawn
				if (empty($fields) && !in_array($post_type, CCTM::get_value($m,'post_types',array()))) {
					continue;
				}


				// See https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=511
                // Check if the user has specified PHP conditions to control the visibility of the metabox
                if ( isset($m['visibility_control']) && !empty($m['visibility_control']) ) {
                    if (strpos($m['visibility_control'],',') !== false) {
                        $templates = array_map('trim', explode(',', $m['visibility_control']));
                    }
                    else {
                        $templates = array(trim($m['visibility_control']));
                    }

                    if (!in_array(basename(get_page_template()), $templates)) {
                        continue; // Skip this metabox if the page template isn't listed here.
                    }

                }				
				add_meta_box($m['id'],$m['title'],$callback,$post_type,$m['context'],$m['priority'],$callback_args);
			}
			// get orphaned custom fields: fields without an explicit metabox
			if (!empty($all_fields)) {
				$m = CCTM::$metabox_def; // default
				$callback = 'StandardizedCustomFields::print_custom_fields';
				$callback_args = array('metabox_id'=>'cctm_default','fields'=>$all_fields);
				add_meta_box($m['id'],$m['title'],$callback,$post_type,$m['context'],$m['priority'],$callback_args);
			}
		}
	}

	//------------------------------------------------------------------------------
	/*
	 * WP only allows users to select PUBLISHED pages of the same post_type in their hierarchical
	 * menus.  And there are no filters for this whole thing save at the end to filter the generated 
	 * HTML before it is sent to the browser. Arrgh... this is grossly inefficient!!
	 * It's inefficient, but here we optionally pimp out the HTML to offer users sensible choices for
	 * hierarchical parents.
	 *
	 * @param	string	incoming html element for selecting a parent page, e.g.
	 *
	 *     <select name="parent_id" id="parent_id">
	 *	       <option value="">(no parent)</option>
	 *		   <option class="level-0" value="706">Post1</option>
	 *	    </select>	
	 *
	 * See http://wordpress.org/support/topic/cannot-select-parent-when-creatingediting-a-page	 
	 */
	public static function customized_hierarchical_post_types($html) {
		global $wpdb, $post;
		
		// Otherwise there be errors on the Settings --> Reading page
		if (empty($post)) {
			return $html;
		}

		$post_type = $post->post_type;
		
		// customize if selected
		if (isset(CCTM::$data['post_type_defs'][$post_type]['hierarchical'])
			&& CCTM::$data['post_type_defs'][$post_type]['hierarchical'] 
			&& CCTM::$data['post_type_defs'][$post_type]['cctm_hierarchical_custom']) {
			// filter by additional parameters
			if ( CCTM::$data['post_type_defs'][$post_type]['cctm_hierarchical_includes_drafts'] ) {
				$args['post_status'] = 'publish,draft,pending';	
			}
			else {
				$args['post_status'] = 'publish';
			}
			
			$args['post_type'] = CCTM::$data['post_type_defs'][$post_type]['cctm_hierarchical_post_types'];
			// We gotta ensure ALL posts are returned.
			// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=114
			$args['numberposts'] 	= -1;
			// And we tweak the order: http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=227
			$args['orderby'] 		= 'title';
			$args['order'] 			= 'ASC';

			$posts = get_posts($args);

			$html = '<select name="parent_id" id="parent_id">
				<option value="">(no parent)</option>
			';
			foreach ( $posts as $p ) {
				$is_selected = '';
				if ( $p->ID == $post->post_parent ) {
					$is_selected = ' selected="selected"';	
				}
				// We add the __() to post_title for the benefit of translation plugins. E.g. see issue 279
				$html .= sprintf('<option class="level-0" value="%s"%s>%s (%s)</option>', $p->ID, $is_selected, __($p->post_title), $p->post_type);
			}
			$html .= '</select>';
		}
		return $html;
	}

	//------------------------------------------------------------------------------
	/**
	 * We use this to print out the large icon at the top of the page when editing a post/page
	 * and to enforce field validation (including required fields).
	 * http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=188
	 * https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=544
	 */
	public static function print_admin_header() {

		$file = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1);
		if ( !in_array($file, array('post.php', 'post-new.php','edit.php'))) {
			return;
		}
		$post_type = CCTM::get_value($_GET, 'post_type');
		if (empty($post_type)) {
			$post_id = (int) CCTM::get_value($_GET, 'post');
			if (empty($post_id)) {
				return;
			}
			$post = get_post($post_id);
			$post_type = $post->post_type;
		}
		
		// Only do this stuff for active post-types (is_active can be 1 for built-in or 2 for foreign)
		if (!isset(CCTM::$data['post_type_defs'][$post_type]['is_active']) || !CCTM::$data['post_type_defs'][$post_type]['is_active']) {
			return; 
		}
		
		// Show the big icon: http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=136
		if ( isset(CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon']) 
			&& CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon'] == 0 ) { 
			$baseimg = basename(CCTM::$data['post_type_defs'][$post_type]['menu_icon']);
			// die($baseimg); 
			if ( file_exists(CCTM_PATH . '/images/icons/32x32/'. $baseimg) ) {
				printf('
				<style>
					#icon-edit, #icon-post {
					  background-image:url(%s);
					  background-position: 0px 0px;
					}
				</style>'
				, CCTM_URL . '/images/icons/32x32/'. $baseimg);
			}
		}
		
		// Validate the custom fields when a page is first printed (e.g. the validation rule was
		// created or updated, then the post is edited)
		$file = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1);
		if ( in_array($file, array('post.php'))) {
			global $post;
		
			$Q = new GetPostsQuery();
			$full_post = $Q->get_post($post->ID);
			
			if(!self::validate_fields($post_type, $full_post)) {
		
				// Print any validation errors.
					$output = '<div class="error"><img src="'.CCTM_URL.'/images/warning-icon.png" width="50" height="44" style="float:left; padding:10px; vertical-align:middle;"/><p style=""><strong>'
						. __('This post has validation errors.  The post will remain in draft status until they are corrected.', CCTM_TXTDOMAIN) 
						. '</strong></p>
						<ul style="clear:both;">';
					
					foreach (CCTM::$post_validation_errors as $fieldname => $e) {
						$output .= '<li>'.$e.'</li>';
					}
					$output .= '</ul></div>';
					
					// You have to print the style because WP overrides styles after the cctm manager.css is included.
					// This isn't helpful during the admin head event because you'd have to also check validation at the time when
					// the fields are printed in print_custom_fields(), which fires later on.
					
					// We can use this variable to pass data to a point later in the page request. 
					// global $cctm_validation;
					// CCTM::$errors 
					// CCTM::$errors['my_field'] = 'This is the validation error with that field';
					
					$output .= '<style>';
					$output .= file_get_contents(CCTM_PATH.'/css/validation.css');
					$output .= '</style>';
		
					print $output;
				
				// Override!! set post to draft status if there were validation errors.
				// See https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=544
				global $wpdb;
				$post_id = (int) CCTM::get_value($_GET, 'post');
				$wpdb->update($wpdb->posts, array('post_status' => 'draft'), array('ID' => $post_id),array('%s'),array('%d'));
			}
		}
	}

	/**
	 * Print Custom Fields inside the given metabox inside the WP manager.
	 *
	 * @param object $post passed to this callback function by WP. 
	 * @param object $callback_args;  $callback_args['args'] contains the 
	 * 	7th parameter from the add_meta_box() function, an array with 
	 *	the metabox_id and fields.
	 *
	 * @return null	this function should print form fields.
	 */
	public static function print_custom_fields($post, $callback_args='') {		

		$metabox_id = CCTM::get_value($callback_args['args'],'metabox_id');
		$custom_fields = CCTM::get_value($callback_args['args'],'fields',array());		
		$post_type = $post->post_type;

		// Output hash for parsing
		$output = array('content'=>'');
		
		foreach ( $custom_fields as $cf ) {
			if (!isset(CCTM::$data['custom_field_defs'][$cf])) {
				// throw error!!
				continue;
			}
			$def = CCTM::$data['custom_field_defs'][$cf];
			
			if (isset($def['required']) && $def['required'] == 1) {
				$def['label'] = $def['label'] . '*'; // Add asterisk
			}
			
			$output_this_field = '';
			if (!$FieldObj = CCTM::load_object($def['type'],'fields')) {
				continue;
			}			
			if ( self::_is_new_post() ) {	
				$FieldObj->set_props($def);
				$output_this_field = $FieldObj->get_create_field_instance();
			}
			else {
				$current_value = get_post_meta( $post->ID, $def['name'], true );
				// Check for validation errors.
				if (isset(CCTM::$post_validation_errors[ $def['name'] ])) {
					$def['error_msg'] = sprintf('<span class="cctm_validation_error">%s</span>', CCTM::$post_validation_errors[ $def['name'] ]);
					if (isset($def['class'])) {
						$def['class'] .= 'cctm_validation_error';
					}
					else {
						$def['class'] = 'cctm_validation_error';
					}
					
				}
				$FieldObj->set_props($def);
				$output_this_field =  $FieldObj->get_edit_field_instance($current_value);
			}
			$output[$cf] = $output_this_field;	
			$output['content'] .= $output_this_field;
		}
		
		// TODO: Print the nonce only once (currently it will print once for each metabox)
		$output['nonce'] = '<input type="hidden" name="_cctm_nonce" value="'.wp_create_nonce('cctm_create_update_post').'" />';
		$output['content'] .= $output['nonce'];

 		// Print the form
 		$metaboxtpl = CCTM::load_tpl(array('metaboxes/'.$metabox_id.'.tpl', 'metaboxes/_default.tpl'));

 		print CCTM::parse($metaboxtpl, $output); 
	}

	//------------------------------------------------------------------------------
	/**
	 * Remove the default Custom Fields meta box. Only affects the content types that
	 * have been activated.
	 * INPUTS: sent from WordPress
	 */
	public static function remove_default_custom_fields($type,$context,$post) {
		$post_types_array = CCTM::get_active_post_types();
		foreach ($post_types_array as $post_type ) {
			remove_meta_box('postcustom',$post_type,'normal');
		}
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Save the new Custom Fields values. If the content type is not active in the 
	 * CCTM plugin or its custom fields are not being standardized, then this function 
	 * effectively does nothing.
	 *
	 * WARNING: This function is also called when the wp_insert_post() is called, and
	 * we don't want to step on its toes. We want this to kick in ONLY when a post 
	 * is inserted via the WP manager. 
	 * see http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=52
	 * 
	 * @param	integer	$post_id id of the post these custom fields are associated with
	 * @param	object	$post  the post object
	 */
	public static function save_custom_fields($post_id, $post) {

		// Bail if you're not in the admin editing a post
		if (!self::_is_existing_post() && !self::_is_new_post() ) {
			return;
		}
		
		// Bail if this post-type is not active in the CCTM
		if ( !isset(CCTM::$data['post_type_defs'][$post->post_type]['is_active']) 
			|| CCTM::$data['post_type_defs'][$post->post_type]['is_active'] == 0) {
			return;
		}
	
		// Bail if there are no custom fields defined in the CCTM
		if ( empty(CCTM::$data['post_type_defs'][$post->post_type]['custom_fields']) ) {
			return;
		}
		
		// See issue http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=80
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
			return $post_id;
		}

		// Use this to ensure you save custom fields only when saving from the edit/create post page
		$nonce = CCTM::get_value($_POST, '_cctm_nonce');
		if (! wp_verify_nonce($nonce, 'cctm_create_update_post') ) {
			return;
		}

		if (!empty($_POST)) {			
			$custom_fields = self::_get_custom_fields($post->post_type);
			$validation_errors = array();
			foreach ( $custom_fields as $field_name ) {
				if (!isset(CCTM::$data['custom_field_defs'][$field_name]['type'])) {
					continue;
				}
				$field_type = CCTM::$data['custom_field_defs'][$field_name]['type'];

				if ($FieldObj = CCTM::load_object($field_type,'fields')) {
					$FieldObj->set_props(CCTM::$data['custom_field_defs'][$field_name]);
					$value = $FieldObj->save_post_filter($_POST, $field_name);

                    CCTM::log("Saving field Type: $field_type  with value: $value",__FILE__,__LINE__);
										
					// Custom fields can return a literal null if they don't save data to the db.
					if ($value !== null) {
					
						// Check for empty json arrays, e.g. [""], convert them to empty PHP array()
						$value_copy = $value;
						if ($FieldObj->is_repeatable) {
							$value_copy = json_decode(stripslashes($value), true);

							if (is_array($value_copy)) {
								foreach ($value_copy as $k => $v) {
									if (empty($v)) {
										unset($value_copy[$k]);
									}
								}
							}
						}

                        // We do some more work to ensure the database stays lean
                        if(is_array($value_copy) && empty($value_copy) && !CCTM::get_setting('save_empty_fields')) {
                            delete_post_meta($post_id, $field_name);
                        }
						if(!is_array($value_copy) && !strlen(trim($value_copy)) && !CCTM::get_setting('save_empty_fields')) {
							// Delete the row from wp_postmeta, or don't write it at all
							delete_post_meta($post_id, $field_name);
						}
						else {
							update_post_meta($post_id, $field_name, $value);
						}
					}					
					
				}
				else {
					// error!  Can't include the field class.  WTF did you do?
				}
			}
			
			// Pass validation errors like this: fieldname => validator, e.g. myfield => required
			if (!empty($validation_errors)) {
                CCTM::log('Validation errors: '.json_encode($validation_errors),__FILE__,__LINE__);
				CCTM::set_flash(json_encode($validation_errors));
			}
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Validate custom fields on a post that's already been saved.
	 *
	 * @param string $post_type
	 * @param array $full_post array of all submitted values
	 * @return boolean : true if valid, false if there were errors
	 */
	public static function validate_fields($post_type,$full_post) {

		$custom_fields = self::_get_custom_fields($post_type);
		$validation_errors = array();
		foreach ( $custom_fields as $field_name ) {
			if (!isset(CCTM::$data['custom_field_defs'][$field_name]['type'])) {
				continue;
			}
			$field_type = CCTM::$data['custom_field_defs'][$field_name]['type'];
			
			if ($FieldObj = CCTM::load_object($field_type,'fields')) {
				$FieldObj->set_props(CCTM::$data['custom_field_defs'][$field_name]);
				$value = '';
				if (isset($full_post[$field_name])) {
					$value = $full_post[$field_name];
				}
				
				// Check for empty json arrays, e.g. [""], convert them to empty PHP array()
				$value_copy = '';
				if ($FieldObj->is_repeatable) {
					$value_copy = $FieldObj->get_value($value, 'to_array');
					if (is_array($value_copy)) {
						foreach ($value_copy as $k => $v) {
							if (empty($v)) {
								unset($value_copy[$k]);
							}
						}
					}
				}
				else {
					$value_copy = $FieldObj->get_value($value, 'to_string');
				}

				// Is this field required?  OR did validation fail?
				if ($FieldObj->required) {
					if ((is_array($value_copy) && empty($value_copy))
						|| (!is_array($value_copy) && !strlen(trim($value_copy)))) {
						CCTM::$post_validation_errors[$FieldObj->name] = sprintf(__('The %s field is required.', CCTM_TXTDOMAIN), $FieldObj->label);
					}
					elseif(!is_array($value_copy) && !strlen(trim($value_copy))) {
						CCTM::$post_validation_errors[$FieldObj->name] = sprintf(__('The %s field is required.', CCTM_TXTDOMAIN), $FieldObj->label);					
					}
				}
				// Do any other validation checks here: TODO
				// see http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=426
				// https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=374
				elseif ((!empty($value_copy) || $value_copy == '0') && isset($FieldObj->validator) && !empty($FieldObj->validator)) {
					$Validator = CCTM::load_object($FieldObj->validator, 'validators');
					if (isset(CCTM::$data['custom_field_defs'][$field_name]['validator_options'])) {
						$Validator->set_options(CCTM::$data['custom_field_defs'][$field_name]['validator_options']);
					}
					$Validator->set_subject($FieldObj->label);
					$Validator->set_options($FieldObj->validator_options);
					if (is_array($value_copy)) {
						foreach ($value_copy as $i => $val) {
							$value_copy[$i] = $Validator->validate($val);
						}
					}
					else {
						$value_copy = $Validator->validate($value_copy);
					}					
					if (!empty($Validator->error_msg)) {
						CCTM::$post_validation_errors[$FieldObj->name] = $Validator->get_error_msg();
					}
				}
				
			}
			else {
				// error!  Can't include the field class.  WTF did you do to get here?
			}
		}

		if (empty(CCTM::$post_validation_errors)) {
			return true;
		}
		else {
			return false;
		}		
	}

} // End of class

/*EOF*/