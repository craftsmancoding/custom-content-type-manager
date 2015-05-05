<?php
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Create a Metabox
------------------------------------------------------------------------------*/
require_once(CCTM_PATH .'/includes/CCTM_Metabox.php');

// Page variables
$data = array();
$data['page_title'] = __('Create Metabox', CCTM_TXTDOMAIN);
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Metaboxes';
$data['msg'] = '';

//$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_fields&a=create_metabox" class="button">%s</a>', __('Create Metabox', CCTM_TXTDOMAIN) );
$data['menu'] = sprintf('<a href="'.get_admin_url(false, 'admin.php').'?page=cctm" title="%s" class="button">%s</a>', __('Cancel'), __('Cancel'));
$data['action_name']  = 'custom_content_type_mgr_create_metaboxes';
$data['nonce_name']  = 'custom_content_type_mgr_create_metaboxes_nonce';
// $data['change_field_type'] = '<br/>';

$field_data = array(); // Data object we will save

// Init Error bits
$data['id.error'] = '';
$data['title.error'] = '';
$data['callback.error'] = '';
$data['id.error_class'] = '';
$data['title.error_class'] = '';
$data['callback.error_class'] = '';
// Init data points
$data['id'] = '';
$data['title'] = '';
$data['context'] = 'advanced';
$data['priority'] = 'default';
$data['callback'] = '';
$data['callback_args'] = '';
$data['visibility_control'] = '';

$data['style'] = file_get_contents(CCTM_PATH.'/css/validation.css');

// Save if submitted...
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	$def = CCTM_Metabox::sanitize($_POST);
	unset($def['old_id']);
	if (CCTM_Metabox::is_valid_def($_POST)) {
		CCTM::$data['metabox_defs'][ $def['id'] ] = $def;
		CCTM::set_flash(CCTM::format_msg(__('Metabox created.',CCTM_TXTDOMAIN)));
	
		$continue_editing = CCTM::get_value($_POST, 'continue_editing');
		unset($_POST);
		if ($continue_editing) {
			CCTM::redirect('?page=cctm&a=edit_metabox&id='.$def['id']);
		}
		else {
			CCTM::redirect('?page=cctm');
		}
		return;
	}
	else {
		$data['msg'] = CCTM::format_error_msg(CCTM_Metabox::$errors, __('Please correct the following problems.',CCTM_TXTDOMAIN));
		foreach (CCTM_Metabox::$errors as $field => $error) {
			$data[$field.'.error'] = sprintf('<span class="cctm_validation_error">%s</span>',$error);
			$data[$field.'.error_class'] = 'cctm_validation_error';
		}
	}

	// Repopulate
	foreach($def as $k => $v) {
		$data[$k] = $v;
	}
}

$data['rows'] = '';


$data['associations'] = '';
// Get the post-types for listing associations.
$displayable_types = self::get_post_types();


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

	$icon = '<img src="'. CCTM_URL . '/images/icons/post.png' . '" width="15" height="15"/>';
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

$data['content'] = CCTM::load_view('metabox.php', $data);
print CCTM::load_view('templates/default.php', $data);


/*EOF*/