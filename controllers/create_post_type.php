<?php
/*------------------------------------------------------------------------------
Create a new post type.
------------------------------------------------------------------------------*/
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
require_once(CCTM_PATH.'/includes/CCTM_PostTypeDef.php');

$data=array();
$data['page_title'] = __('Create Custom Content Type', CCTM_TXTDOMAIN);
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/CreatePostType';
$data['msg'] = '';
$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm" title="%s" class="button">%s</a>', __('Cancel'), __('Cancel'));
$data['edit_warning'] = ''; // only used when you edit a post_type, not delete.

// Variables for our template

$fields   = '';

$data['action_name']  = 'custom_content_type_mgr_create_new_content_type';
$data['nonce_name']  = 'custom_content_type_mgr_create_new_content_type_nonce';
$data['submit']   = __('Create New Content Type', CCTM_TXTDOMAIN);
$data['action'] = 'create';

$data['post_type'] = ''; // as default
$data['def'] = self::$default_post_type_def;
//		$def = self::$post_type_form_definition;

// Save data if it was properly submitted
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	$sanitized_vals	= CCTM_PostTypeDef::sanitize_post_type_def($_POST);
	$error_msg 		= CCTM_PostTypeDef::post_type_name_has_errors($sanitized_vals, true);

	if ( empty($error_msg) ) {
		// Clean slate.  This nukes any instance of 'is_foreign' (and potentially other issues)
		// that may arise if the post-type name was used by another plugin and the CCTM tracked
		// custom fields for that plugin, and then later the other plugin was deactivated and 
		// the CCTM wants to use the same post-type name.
		unset(CCTM::$data['post_type_defs'][ $sanitized_vals['post_type'] ]);
		CCTM_PostTypeDef::save_post_type_settings($sanitized_vals);
		
		$data['msg'] = CCTM::format_msg( sprintf(__('The content type %s has been created', CCTM_TXTDOMAIN), '<em>'.$sanitized_vals['post_type'].'</em>'));
		self::set_flash($data['msg']);
		include CCTM_PATH . '/controllers/list_post_types.php';
		return;
	}
	else {
		// clean up... menu labels in particular can get gunked up. :(
		$data['def']  = $sanitized_vals;
		$data['def']['labels']['singular_name'] = '';
		$data['def']['label'] = '';
		$data['msg'] = CCTM::format_error_msg($error_msg);
	}
}
$data['icons'] = CCTM_PostTypeDef::get_post_type_icons();
$data['columns'] = CCTM_PostTypeDef::get_columns($post_type);
$data['orderby_options'] = CCTM_PostTypeDef::get_orderby_options($post_type);
$data['content'] = CCTM::load_view('post_type.php', $data);
print CCTM::load_view('templates/default.php', $data);
/*EOF*/