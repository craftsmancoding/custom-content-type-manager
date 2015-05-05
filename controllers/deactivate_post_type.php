<?php
/*------------------------------------------------------------------------------
Deactivate a post type. This will remove custom post types from the WP menus;
deactivation stops custom fields from being standardized in built-in and custom
post types

$post_type
------------------------------------------------------------------------------*/
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
require_once(CCTM_PATH.'/includes/CCTM_PostTypeDef.php');

// Validate post type
if (!CCTM_PostTypeDef::is_existing_post_type($post_type) ) {
	include(CCTM_PATH.'/controllers/error.php');
	return;
}

// Variables for our template
$data 				= array();
$data['page_title']	= sprintf( __('Deactivate Content Type %s', CCTM_TXTDOMAIN), $post_type );
$data['help']		= 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/DeactivatePostType';
$data['menu'] 		= '';
$data['msg']		= '';
$data['content'] =  '';

$fields   = '';
$data['action_name']  = 'custom_content_type_mgr_deactivate_content_type';
$data['nonce_name']  = 'custom_content_type_mgr_deactivate_content_type_nonce';
$data['submit']   = __('Deactivate', CCTM_TXTDOMAIN);

// If properly submitted, Proceed with deleting the post type
// OR if it's a built-in post-type OR a foreign post-type
$is_built_in = false;
if (in_array($post_type, CCTM::$built_in_post_types )) {
	$is_built_in = true;
}
$is_foreign = false;
if (!isset(self::$data['post_type_defs'][$post_type]['post_type'])) {
	$is_foreign = true;
}
if ( ($is_foreign || $is_built_in) || !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	// get current values from database
	self::$data['post_type_defs'][$post_type]['is_active'] = 0;
	update_option( self::db_key, self::$data );

	$data['msg'] = '<div class="updated"><p>'
		. sprintf( __('The %s content type has been deactivated.', CCTM_TXTDOMAIN), $post_type )
		. '</p></div>';
	self::set_flash($data['msg']);

	// A JavaScript refresh ensures that inactive post types are removed from the menus.
	$data['msg'] = '
	<script type="text/javascript">
		window.location.replace("?page=cctm");
	</script>';
	print $data['msg'];
	return;
}

$post_cnt_obj = wp_count_posts($post_type);

$data['content'] = '<div class="error">
	<img src="'.CCTM_URL.'/images/warning-icon.png" width="50" height="44" style="float:left; padding:10px;"/>
	<p>'
	. sprintf( __('You are about to deactivate the %s post type.', CCTM_TXTDOMAIN ), "<strong>$post_type</strong>")
	.'</p>';

// If it's a custom post type, we include some additional info.
if ( !in_array($post_type, self::$built_in_post_types) ) {
	$data['content'] .= '<p>'
		. sprintf( __('Deactivation does not delete anything, but it does make %s posts unavailable to the outside world. %s will be removed from the administration menus and you will no longer be able to edit them using the WordPress manager.', CCTM_TXTDOMAIN), "<strong>$post_type</strong>", "<strong>$post_type</strong>" )
		.'</p>';

	$data['content'] .= '<p>'
		. sprintf( __('This would affect %1$s published %2$s posts.'
			, CCTM_TXTDOMAIN), '<strong>'.$post_cnt_obj->publish.'</strong>'
		, "<strong>$post_type</strong>")
		.'</p>';
}
else {
	$data['content'] .= '<p>'
		. sprintf( __('Deactivation does not delete anything, but it does turn off the standardization of the custom fields.  By default WordPress only supports simple text fields and you can easily misspell field names.', CCTM_TXTDOMAIN), "<strong>$post_type</strong>", "<strong>$post_type</strong>" )
		.'</p>';

}


$data['content'] .= '<p>'.__('Are you sure you want to do this?', CCTM_TXTDOMAIN).'
		</p>
	</div>';


$data['content'] = CCTM::load_view('basic_form.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/