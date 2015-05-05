<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
* Confirm Delete/Deletes a custom field definition
* @param string $field_name (unique name of field)
------------------------------------------------------------------------------*/

$data 				= array();
$data['page_title']	= sprintf( __('Delete Custom Field: %s', CCTM_TXTDOMAIN), $field_name );
$data['menu'] 		= ''; 
$data['msg']		= CCTM::get_flash();
$data['action_name'] = 'custom_content_type_mgr_delete_field';
$data['nonce_name'] = 'custom_content_type_mgr_delete_field_nonce';
$data['submit']   = __('Delete', CCTM_TXTDOMAIN);
$data['fields']   = '';

// Make sure the field exists
if (!array_key_exists($field_name, self::$data['custom_field_defs'])) {
    //print '<pre>'.print_r(self::$data['custom_field_defs'],true).'</pre>'; exit;
	$msg_id = 'invalid_field_name';
	include(CCTM_PATH.'/controllers/error.php');
	return;
}

// If properly submitted, Proceed with deleting the Custom Field def
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	unset(self::$data['custom_field_defs'][$field_name]); 
	// remove any references to this field from the post_type data.
	$defs = self::get_post_type_defs();
	foreach ($defs as $pt => $d ) {
		if (isset($d['custom_fields']) && in_array($field_name, $d['custom_fields'])) {
			$defs[$pt]['custom_fields'] = array(); // reset, so we can rebuild it w/o the field
			foreach ($d['custom_fields'] as $cf) {
				if ($cf != $field_name) {
					$defs[$pt]['custom_fields'][] = $cf;
				}
			}
		}
	}
	self::$data['post_type_defs'] = $defs;
	
	update_option( self::db_key, self::$data );
	
	// Optionally delete rows from wp_postmeta
	if (isset(self::$data['settings']['delete_custom_fields']) && self::$data['settings']['delete_custom_fields']) {
		global $wpdb;
		$query = $wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE meta_key=%s;"
			, $field_name);
		$wpdb->query($query);
	}
	
	$msg = '<div class="updated"><p>'
		.sprintf( __('The custom field %s has been deleted', CCTM_TXTDOMAIN), "<em>$field_name</em>")
		. '</p></div>';
	self::set_flash($msg);
	include( CCTM_PATH . '/controllers/list_custom_fields.php');
	return;
}


// Warn about the actual deletion
if (isset(self::$data['settings']['delete_custom_fields']) && self::$data['settings']['delete_custom_fields']) {
	
	$data['content'] = '<div class="error">
		<img src="'.CCTM_URL.'/images/warning-icon.png" width="50" height="44" style="float:left; padding:10px;"/>
		<p>'
		. sprintf( __('You are about to delete the %s custom field and delete all of its values from the database. This cannot be undone!', CCTM_TXTDOMAIN), "<em>$field_name</em>" )
		.'</p>'
		. '<p>'.__('Are you sure you want to do this?', CCTM_TXTDOMAIN).'
		<a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/DeletePostType" title="Deleting a content type" target="_blank">
		<img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" />
		</a>
		</p></div>';
}
// Warn about the chaos of having no def
else {
	$data['content'] = '<div class="error">
		<img src="'.CCTM_URL.'/images/warning-icon.png" width="50" height="44" style="float:left; padding:10px;"/>
		<p>'
		. sprintf( __('You are about to delete the %s custom field. This will remove all of its settings from the database, but this will NOT delete any data from the wp_postmeta table. However, without a definition for this field, it will be mostly invisible to WordPress.', CCTM_TXTDOMAIN), "<em>$field_name</em>" )
		.'</p>'
		. '<p>'.__('Are you sure you want to do this?', CCTM_TXTDOMAIN).'
		<a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/DeletePostType" title="Deleting a content type" target="_blank">
		<img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" />
		</a>
		</p></div>';

}		
$data['content'] = CCTM::load_view('basic_form.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/