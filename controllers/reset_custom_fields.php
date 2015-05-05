<?php
/*------------------------------------------------------------------------------
Deletes all custom field definitions for a given post_type.
@param string $post_type
------------------------------------------------------------------------------*/
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
require_once(CCTM_PATH.'/includes/CCTM_PostTypeDef.php');


// We can't delete built-in post types
if (!CCTM_PostTypeDef::is_existing_post_type($post_type, true ) ) {
	include(CCTM_PATH.'/controllers/error.php');
	return;
}

// Variables for our template
$page_header = __('Reset custom field definitions', CCTM_TXTDOMAIN);
$fields   		= '';
$action_name 	= 'custom_content_type_mgr_delete_all_custom_fields';
$nonce_name 	= 'custom_content_type_mgr_delete_all_custom_fields';
$submit   		= __('Reset', CCTM_TXTDOMAIN);

// If properly submitted, Proceed with deleting the post type
if ( !empty($_POST) && check_admin_referer($action_name, $nonce_name) ) {

	unset(self::$data[$post_type]['custom_fields']); // <-- Delete this node of the data structure
	update_option( self::db_key, self::$data );
	$msg = '<div class="updated"><p>'
		.sprintf( __('All custom field definitions for the %s post type have been deleted', CCTM_TXTDOMAIN), "<em>$post_type</em>")
		. '</p></div>';
	self::set_flash($msg);
	self::_page_show_custom_fields($post_type, true);
	return;
}

$msg = '<div class="error">
	<img src="'.CCTM_URL.'/images/warning-icon.png" width="50" height="44" style="float:left; padding:10px;"/>
	<p>'
	. sprintf( __('You are about to delete all custom field definitions for the %s post type. This will not delete any data from the wp_postmeta table, but it will make any custom fields invisible to WordPress users on the front and back end.', CCTM_TXTDOMAIN), "<em>$post_type</em>" )
	.'</p>'
	. '<p>'.__('Are you sure you want to do this?', CCTM_TXTDOMAIN).'</p></div>';

// The URL nec. to take the "Cancel" button back to this page.
$cancel_target_url = '?page='.self::admin_menu_slug . '&'.self::action_param .'=4&'.self::post_type_param.'='.$post_type;

include 'pages/basic_form.php';

