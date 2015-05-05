<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
* Edit a custom field.  
*
* @param string $field_type identifies the type of field we're editing
* @param string $field_name	uniquely identifies this field inside this post_type

------------------------------------------------------------------------------*/
// Make sure the field exists
if (!array_key_exists($field_name, self::$data['custom_field_defs'])) {
	$msg_id = 'invalid_field_name';
	include(CCTM_PATH.'/controllers/error.php');
	return;
}


// Page variables
$data = array();
$data['page_title'] = sprintf(__('Edit Custom Field: %s', CCTM_TXTDOMAIN), $field_name );
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/CustomFieldDefinitions';
$data['msg'] = '';
$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_fields&a=list_custom_fields" title="%s" class="button">%s</a>', __('Cancel'), __('Cancel'));
$data['submit'] = __('Save', CCTM_TXTDOMAIN);
$data['action_name']  = 'custom_content_type_mgr_edit_custom_field';
$data['nonce_name']  = 'custom_content_type_mgr_edit_custom_field_nonce';

$nonce = CCTM::get_value($_GET, '_wpnonce');
if (! wp_verify_nonce($nonce, 'cctm_edit_field') ) {
	die( __('Invalid request.', CCTM_TXTDOMAIN ) );
}

// Get the post-types for listing associations.
$displayable_types = CCTM::get_post_types();
	
$field_type = self::$data['custom_field_defs'][$field_name]['type'];
$field_data = self::$data['custom_field_defs'][$field_name]; // Data object we will save

if(!$FieldObj = CCTM::load_object($field_type, 'fields')) {
	die('Field not found.');
}

$field_data['original_name'] = $field_name;
$FieldObj->set_props($field_data);  

$data['change_field_type'] = '<br/><a href="?page=cctm_fields&a=change_field_type&field='. $field_name. '&_wpnonce='. wp_create_nonce('cctm_change_field_type'). '" class="button">'. __('Change Field Type', CCTM_TXTDOMAIN) .'</a></p>';



// Save if submitted...
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	// A little cleanup before we handoff to save_definition_filter
	unset($_POST[ $data['nonce_name'] ]);
	unset($_POST['_wp_http_referer']);

	// Handle editing of the associations
	// All associations were removed
	$post_type_defs = CCTM::get_post_type_defs();
	
	if ( !isset($_POST['post_types'])) {
		// die('All associations removed...');
		foreach($displayable_types as $pt) {
			$def = array();
			if ( isset(CCTM::$data['post_type_defs'][$pt])) {
				$def = CCTM::$data['post_type_defs'][$pt];
			}
			
			if (is_array($def['custom_fields']) && in_array($field_name, $def['custom_fields'])) {
				$revised_custom_fields = array();
				foreach ($def['custom_fields'] as $cf) {
					if ( $cf != $field_name ) {
						$revised_custom_fields[] = $cf;
					}
				}
				CCTM::$data['post_type_defs'][$pt]['custom_fields'] = $revised_custom_fields;
			}
		}
	}
	else {
		foreach($displayable_types as $pt) {
			$def = array();
			if ( isset(CCTM::$data['post_type_defs'][$pt])) {
				$def = CCTM::$data['post_type_defs'][$pt];
			}
			
			// Add the association
			if (in_array($pt, $_POST['post_types'])) {
				if (!isset($def['custom_fields']) 
					|| !is_array($def['custom_fields'])
					|| !in_array($field_name, $def['custom_fields'])) {
					$def['custom_fields'][] = $field_name;
					CCTM::$data['post_type_defs'][$pt]['custom_fields'] = $def['custom_fields'];
				}
			}
			// Remove the association
			else {
				$revised_custom_fields = array();
				if (isset($def['custom_fields'])) {
					foreach ($def['custom_fields'] as $cf) {
						if ( $cf != $field_name ) {
							$revised_custom_fields[] = $cf;
						}
					}
				}
				CCTM::$data['post_type_defs'][$pt]['custom_fields'] = $revised_custom_fields;								
			}
		}
	}
	// Clear this up... the rest of the data is for the field definition.
	unset($_POST['post_types']);


	// Validate and sanitize any submitted data
	$field_data 		= $FieldObj->save_definition_filter($_POST);
	$field_data['type'] = $field_type; // same effect as adding a hidden field
	$FieldObj->set_props($field_data); // used for repopulating on errors

	// Any errors?
	if ( !empty($FieldObj->errors) ) {
		$data['msg'] = $FieldObj->format_errors();
	}
	// Save;
	else {
		// die(print_r($field_data,true));
		// Unset the old field if the name changed ($field_name is passed via $_GET)
		if ($field_name != $field_data['name']) {
			unset(self::$data['custom_field_defs'][$field_name]);
			// update database... but what if the new name is taken?
		}
		self::$data['custom_field_defs'][ $field_data['name'] ] = $field_data;
		update_option( self::db_key, self::$data );
		$continue_editing = CCTM::get_value($_POST, 'continue_editing');
		unset($_POST);
		
		
		$data['msg'] = sprintf('<div class="updated"><p>%s</p></div>'
			, sprintf(__('The %s custom field has been edited.', CCTM_TXTDOMAIN)
			, '<em>'.$field_name.'</em>'));		
		self::set_flash($data['msg']);
		
		if (!$continue_editing) {
			include(CCTM_PATH.'/controllers/list_custom_fields.php');
			return;
		}
		$FieldObj->set_props($field_data);
	}
}


$data['icon'] = sprintf('<img src="%s" class="cctm-field-icon" id="cctm-field-icon-%s"/>'
				, $FieldObj->get_icon(), $field_type);
$data['url'] = $FieldObj->get_url();
if (empty($FieldObj->label)) {
	$data['name'] = $FieldObj->get_name();
}
else {
	$data['name'] = $FieldObj->label; //$FieldObj->get_name();
}

$data['description'] = htmlentities($FieldObj->get_description());

$data['fields'] = $FieldObj->get_edit_field_definition($FieldObj->get_props());
$data['associations'] = ''; // TODO


//------------------------------------------------------------------------------
// Get field associations: which post-types does this field belong to
//------------------------------------------------------------------------------
$data['associations'] .= '<table>';
foreach ($displayable_types as $post_type) {
	$def = array();
	$def['description'] = '';
	
	if (isset(self::$data['post_type_defs'][$post_type])) {
		$def = self::$data['post_type_defs'][$post_type];
	}

	$icon = '';
	$target_url = sprintf(
		'<a href="?page=cctm&a=list_pt_associations&pt=%s" title="%s">%s</a>'
		, $post_type
		, __('Manage Custom Fields for this content type', CCTM_TXTDOMAIN)
		, __('Manage Custom Fields', CCTM_TXTDOMAIN)
	);


	//------------------------------------------------------------------------------
	// post,page: Built-in post types
	//------------------------------------------------------------------------------
	if ( in_array($post_type, CCTM::$built_in_post_types) ) {
		$def['description']	= '<img src="'. CCTM_URL .'/images/wp.png" height="16" width="16" alt="wp" /> '. __('Built-in post-type.', CCTM_TXTDOMAIN);
		if ('page' == $post_type) {
			$icon = '<img src="'. CCTM_URL . '/images/icons/page.png' . '" width="14" height="16"/>';
		}
		else {
			$icon = '<img src="'. CCTM_URL . '/images/icons/post.png' . '" width="15" height="15"/>';
		}
	}
	//------------------------------------------------------------------------------
	// Full fledged CCTM post-types
	//------------------------------------------------------------------------------
	elseif (isset(CCTM::$data['post_type_defs'][$post_type]['post_type'])) {
		$def['description'] = self::$data['post_type_defs'][$post_type]['description'];
		if ( !empty($def['menu_icon']) && !$def['use_default_menu_icon'] ) {
			$icon = '<img src="'. $def['menu_icon'] . '" />';
		}
	}
	//------------------------------------------------------------------------------
	// Foreign post-types
	//------------------------------------------------------------------------------
	elseif(self::get_setting('show_foreign_post_types')) {
		$def['description']	= '<img src="'. CCTM_URL .'/images/spy.png" height="16" width="16" alt="wp" /> '. __('Foreign post-type.', CCTM_TXTDOMAIN);
		$icon = '<img src="'. CCTM_URL . '/images/forbidden.png' . '" width="16" height="16"/>';
	
		$target_url = sprintf(
			'<a href="?page=cctm&a=list_pt_associations&pt=%s&f=1" title="%s">%s</a>'
			, $post_type
			, __('Manage Custom Fields for this content type', CCTM_TXTDOMAIN)
			, __('Manage Custom Fields', CCTM_TXTDOMAIN)
		);

	}
	else {
		continue; 
	}


	$is_checked = '';

	if ( isset(self::$data['post_type_defs'][$post_type]['custom_fields']) 
		&& in_array($field_name, self::$data['post_type_defs'][$post_type]['custom_fields'])) {
		$is_checked = ' checked="checked"';
	}
	
	$post_type_label = '<span style="color:gray;">'.$post_type.'</span>';
	if (isset(self::$data['post_type_defs'][$post_type]['is_active']) && self::$data['post_type_defs'][$post_type]['is_active']) {
		$post_type_label = $post_type; // keep it black
	}
	
	$data['associations'] .= sprintf('
		<tr>
			<td><input type="checkbox" name="post_types[]" id="%s" value="%s" %s/></td>
			<td>%s</td>
			<td><label for="%s" class="cctm_label">%s</label></td>
			<td><span class="cctm_description" style="margin-left:20px;">%s</span><td>
			<td>%s</td>
		</tr>'
		, $post_type
		, $post_type
		, $is_checked
		, $icon
		, $post_type
		, $post_type_label
		, $def['description']
		, $target_url
	);
}

$data['associations'] .= '</table>';
$data['field_name'] = $field_name;
$data['field_type'] = $FieldObj->get_name();


$data['content'] = CCTM::load_view('custom_field.php', $data);
print CCTM::load_view('templates/default.php', $data);
/*EOF*/