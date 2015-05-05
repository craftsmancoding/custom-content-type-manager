<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');

/*------------------------------------------------------------------------------
Duplicate an existing custom field of the type specified by $field_type.  

$field_type is set in the $_GET array
------------------------------------------------------------------------------*/

// Make sure the field exists
if (!array_key_exists($field_name, self::$data['custom_field_defs'])) {
	$msg_id = 'invalid_field_name';
	include(CCTM_PATH.'/controllers/error.php');
	return;
}


// Page variables
$data = array();
$data['page_title'] = sprintf(__('Duplicate Custom Field: %s', CCTM_TXTDOMAIN), $field_name );
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/SupportedCustomFields';
$data['msg'] = '';
$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_fields&a=list_custom_fields" title="%s" class="button">%s</a>', __('Cancel'), __('Cancel'));
$data['submit'] = __('Save', CCTM_TXTDOMAIN);
$data['action_name']  = 'custom_content_type_mgr_edit_custom_field';
$data['nonce_name']  = 'custom_content_type_mgr_edit_custom_field_nonce';
$data['change_field_type'] = '';

$nonce = self::get_value($_GET, '_wpnonce');
if (! wp_verify_nonce($nonce, 'cctm_edit_field') ) {
	die( __('Invalid request.', CCTM_TXTDOMAIN ) );
}
		
	
$field_type = self::$data['custom_field_defs'][$field_name]['type'];
$field_data = self::$data['custom_field_defs'][$field_name]; // Data object we will save

if(!$FieldObj = CCTM::load_object($field_type, 'fields')) {
	die('Field not found.');
}


$field_data['name'] = $field_data['name'] . '_copy';
$field_data['label'] = $field_data['label'] . ' Copy';

$FieldObj->props 	= $field_data;  


// Save if submitted...
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	// A little cleanup before we handoff to save_definition_filter
	unset($_POST[ $data['nonce_name'] ]);
	unset($_POST['_wp_http_referer']);

	// Validate and sanitize any submitted data
	$field_data 		= $FieldObj->save_definition_filter($_POST, $post_type);
	$field_data['type'] = $field_type; // same effect as adding a hidden field
	
	$field_data['sort_param'] = 0; // default: up top
	
	$FieldObj->props 	= $field_data;  // This is how we repopulate data in the create forms

	// Any errors?
	if ( !empty($FieldObj->errors) ) {
		$data['msg'] = $FieldObj->format_errors();
	}
	// Save;
	else {
		$field_name = $field_data['name']; 
		self::$data['custom_field_defs'][$field_name] = $field_data;

		// We need this info for associations later:
		$associations = array();
		if (isset($_POST['post_types'])) {
			$associations = $_POST['post_types'];
		}
		unset($_POST['post_types']);
	
		if ( !empty($associations)) {
			
			foreach($associations as $pt) {
				$def = array();
				if ( isset(self::$data['post_type_defs'][$pt])) {
					$def = self::$data['post_type_defs'][$pt];
				}
								
				if (isset($def['custom_fields']) && is_array($def['custom_fields']) && !in_array($field_name, $def['custom_fields'])) {
					$revised_custom_fields = $def['custom_fields'];
					$revised_custom_fields[] = $field_name;
					self::$data['post_type_defs'][$pt]['custom_fields'] = $revised_custom_fields;
				}
				// For previously unused post-types
				else {
					self::$data['post_type_defs'][$pt]['custom_fields'] = array($field_name);
				}
			}
		}

	
	
		update_option( self::db_key, self::$data );
		unset($_POST);
		$success_msg = sprintf('<div class="updated"><p>%s</p></div>'
			, sprintf(__('A %s custom field has been created.', CCTM_TXTDOMAIN)
			, '<em>'.$FieldObj->get_name().'</em>'));
		self::set_flash($success_msg);
		include(CCTM_PATH.'/controllers/list_custom_fields.php');
		return;
	}

}

$data['icon'] = sprintf('<img src="%s" class="cctm-field-icon" id="cctm-field-icon-%s"/>'
				, $FieldObj->get_icon(), $field_type);
$data['url'] = $FieldObj->get_url();
$data['name'] = $FieldObj->get_name();
$data['description'] = htmlspecialchars($FieldObj->get_description());

$data['fields'] = $FieldObj->get_edit_field_definition($field_data);


//------------------------------------------------------------------------------
// Get field associations: which post-types does this field belong to
//------------------------------------------------------------------------------
// Get the post-types for listing associations.
$displayable_types = self::get_post_types();

$data['associations'] = '<table>';
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
		, $post_type
		, $def['description']
		, $target_url
	);
}

$data['associations'] .= '</table>';


$data['content'] = CCTM::load_view('custom_field.php', $data);
print CCTM::load_view('templates/default.php', $data);
/*EOF*/