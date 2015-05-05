<?php
/**
* Duplicate an existing post type. 
* @param string $post_type
*/
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
require_once(CCTM_PATH.'/includes/CCTM_PostTypeDef.php');

// Variables for our template
$data = array();
$d = array();
if ( isset(CCTM::$data['post_type_defs'][$post_type])) {
	$d['def'] = CCTM::$data['post_type_defs'][$post_type];
	// Older definitions may be missing nodes, so we fill from
	// the default in order to avoid "Undefined index" notices
	foreach(CCTM::$default_post_type_def as $k => $v) {
		if (!isset($d['def'][$k])) {
			$d['def'][$k] = $v;
		}
	}
	// Unset/alter stuff that we don't want duplicated
	$d['post_type'] = $post_type . '_copy';
	
	//print_r($d['def']['labels']); exit;
	$menu_name = $d['def']['labels']['menu_name'] . ' Copy';
	foreach($d['def']['labels'] as $k => $v) {
		$d['def']['labels'][$k] = ''; // blank out the labels
	}
	// except for this one
	$d['def']['labels']['menu_name'] = $menu_name;
	
	// print_r($d); exit;
	
}
// Oops... bail.
else {
	$data['msg'] = sprintf('<div class="error"><p>%s</p></div>', __('Unrecognized post_type.', CCTM_TXTDOMAIN));
	$data['page_title']  = __('Unrecognized post-type', CCTM_TXTDOMAIN);
	$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/CreatePostType';
	$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm" title="%s" class="button">%s</a>', __('Back'), __('Back'));
	$data['content'] = '';
	print CCTM::load_view('templates/default.php', $data);
	return;
}

//$d['post_type'] = $post_type;
$d['edit_warning'] = '';

$data['page_title']  = __('Duplicate Content Type: ') . $post_type;
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/CreatePostType';
$fields   = '';
$data['msg'] = '';
$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm" title="%s" class="button">%s</a>', __('Cancel'), __('Cancel'));

$d['action_name'] = 'custom_content_type_mgr_edit_content_type';
$d['nonce_name'] = 'custom_content_type_mgr_edit_content_type_nonce';
$d['submit']   = __('Save', CCTM_TXTDOMAIN);

$d['msg']    = '';  // Any validation errors


// Save data if it was properly submitted
if ( !empty($_POST) && check_admin_referer($d['action_name'], $d['nonce_name']) ) {
	$sanitized_vals = CCTM_PostTypeDef::sanitize_post_type_def($_POST);
	if (isset($d['def']['custom_fields'])) {
		$sanitized_vals['custom_fields'] = $d['def']['custom_fields'];
	}
	else {
		$sanitized_vals['custom_fields'] = array();
	}
	
	$error_msg 		= CCTM_PostTypeDef::post_type_name_has_errors($sanitized_vals, true);

	if ( empty($error_msg) ) {
		CCTM_PostTypeDef::save_post_type_settings($sanitized_vals);
		$data['msg'] = '
		<div class="updated">
			<p>'
			. sprintf( __('The content type %s has been created', CCTM_TXTDOMAIN), '<em>'.$sanitized_vals['post_type'].'</em>')
			. '</p>
		</div>';
		self::set_flash($data['msg']);
		include CCTM_PATH . '/controllers/list_post_types.php';
		return;
	}
	else {
		// clean up... menu labels in particular can get gunked up. :(
		$data['def']  = $sanitized_vals;
		$data['def']['labels']['singular_name'] = '';
		$data['def']['label'] = '';
		$data['msg'] = "<div class='error'>$error_msg</div>";
	}
}

$d['icons'] = CCTM_PostTypeDef::get_post_type_icons();
$data['content'] = CCTM::load_view('post_type.php', $d);
print CCTM::load_view('templates/default.php', $data);
/*EOF*/