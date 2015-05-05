<?php
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
* Confirm Delete/Deletes a metabox definition
* @param string $id (unique name of metabox)
------------------------------------------------------------------------------*/
require_once(CCTM_PATH .'/includes/CCTM_Metabox.php');
//print_r(CCTM::$data['metabox_defs']); exit;
// Page variables
$data = array();

$id = self::get_value($_GET, 'id');
if (!$id) {
	die( __('Invalid request.', CCTM_TXTDOMAIN ) );
}
$data = CCTM::get_value(CCTM::$data['metabox_defs'], $id);
if (empty($data)) {
	$msg_id = 'invalid_metabox_id';
	include(CCTM_PATH.'/controllers/error.php');
	return;
}

$data['page_title']	= sprintf( __('Delete Metabox: %s', CCTM_TXTDOMAIN), $id );
$data['menu'] 		= ''; 
$data['msg']		= CCTM::get_flash();
$data['action_name'] = 'custom_content_type_mgr_delete_metabox';
$data['nonce_name'] = 'custom_content_type_mgr_delete_metabox';
$data['submit']   = __('Delete', CCTM_TXTDOMAIN);
$data['fields']   = '';


// If properly submitted, Proceed with deleting the metabox
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
    // Remove the primary definition
	unset(CCTM::$data['metabox_defs'][$id]); 
	// Remove the map_field_metabox for each post-type so any fields point to the default.
	// See: https://code.google.com/p/wordpress-custom-content-type-manager/wiki/DataStructures
	$defs = self::get_post_type_defs();
	foreach ($defs as $pt => $d ) {
		if (isset($d['map_field_metabox']) && in_array($id, array_values($d['map_field_metabox']))) {
			foreach ($d['map_field_metabox'] as $cf => $mb) {
				if ($mb == $id) {
					$defs[$pt]['map_field_metabox'][$cf] = 'default'; 
				}
			}
		}
	}
	self::$data['post_type_defs'] = $defs;
	
	update_option( self::db_key, self::$data );
		
	$msg = '<div class="updated"><p>'
		.sprintf( __('The Metabox %s has been deleted', CCTM_TXTDOMAIN), "<em>$id</em>")
		. '</p></div>';
	self::set_flash($msg);
	include( CCTM_PATH . '/controllers/list_post_types.php');
	return;
}



$data['content'] = '<div class="error">
	<img src="'.CCTM_URL.'/images/warning-icon.png" width="50" height="44" style="float:left; padding:10px;"/>
	<p>'
	. sprintf( __('You are about to delete the %s Metabox. Any fields that have been assigned to this Metabox will be moved into the default Metabox.  This will not delete any of your custom fields, but it may make your admin pages to become disorganized.', CCTM_TXTDOMAIN), "<em>$id</em>" )
	.'</p>'
	. '<p>'.__('Are you sure you want to do this?', CCTM_TXTDOMAIN).'
	<a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Metaboxes" title="Deleting a Metabox" target="_blank">
	<img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" />
	</a>
	</p></div>';


$data['content'] = CCTM::load_view('basic_form.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/