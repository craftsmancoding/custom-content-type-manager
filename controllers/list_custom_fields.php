<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
//------------------------------------------------------------------------------
/**
 * Manage all custom fields.
 *
 * @param string $post_type
 * @param boolen $reset true only if we've just reset all custom fields
 */
$data=array();
$data['page_title'] = __('Manage Custom Fields', CCTM_TXTDOMAIN);
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/DefinedCustomFields';
$data['msg'] = self::get_flash();
$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_fields&a=list_custom_field_types" class="button">%s</a>', __('Create Custom Field', CCTM_TXTDOMAIN) );
$data['menu'] .= ' ' . sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_fields&a=bulk_add_fields" class="button">%s</a>', __('Bulk Add Fields', CCTM_TXTDOMAIN) );

// Load 'em up
$defs = CCTM::get_custom_field_defs();
/*
unset($defs['status']);
self::$data['custom_field_defs'] = $defs;
update_option( self::db_key, self::$data );
exit;
*/
$def_cnt = count($defs);

if (!isset($reset) && !$def_cnt ) {
	$data['msg'] .= sprintf('<div class="updated"><p>%s</p></div>'
		, __('There are no custom fields defined. Click the button below to add a custom field.', CCTM_TXTDOMAIN));
}

$data['fields'] = '';

foreach ($defs as $field_name => $d) {
	//print_r($defs); exit;
	$d['name'] = $field_name; // just in case the key and the 'name' got out of sync.
	
	if (isset($d['required']) && $d['required']) {
		$d['label'] = $d['label'] . ' *'; // Asterix for req'd fields
	}

	if (!$FieldObj = CCTM::load_object($d['type'],'fields') ) {
		continue;
	}

	$FieldObj->set_props($d);
	$d['icon'] 	= $FieldObj->get_icon();

	if ( !CCTM::is_valid_img($d['icon']) ) {
		$icon_src = self::get_custom_icons_src_dir() . 'default.png';
	}

	$d['icon'] = sprintf('<a href="?page=cctm_fields&a=edit_custom_field&field=%s&_wpnonce=%s" title="%s">
		<img src="%s" style="float:left; margin:5px;"/></a>'
		, $d['name']
		, wp_create_nonce('cctm_edit_field')
		, __('Edit this custom field', CCTM_TXTDOMAIN)
		, $d['icon']);

	
	$d['edit'] = __('Edit');
	$d['delete'] = __('Delete');
	$d['edit_field_link'] = sprintf(
		'<a href="?page=cctm_fields&a=edit_custom_field&field=%s&_wpnonce=%s" title="%s">%s</a>'
		, $d['name']
		, wp_create_nonce('cctm_edit_field')
		, __('Edit this custom field', CCTM_TXTDOMAIN)
		, __('Edit', CCTM_TXTDOMAIN)
	);
	$d['duplicate_field_link'] = sprintf(
		'<a href="?page=cctm_fields&a=duplicate_custom_field&field=%s&type=%s&_wpnonce=%s" title="%s">%s</a>'
		, $d['name']
		, $d['type']
		, wp_create_nonce('cctm_edit_field')
		, __('Duplicate this custom field', CCTM_TXTDOMAIN)
		, __('Duplicate', CCTM_TXTDOMAIN)
	);
	$d['delete_field_link'] = sprintf(
		'<a href="?page=cctm_fields&a=delete_custom_field&field=%s&_wpnonce=%s" title="%s">%s</a>'
		, $d['name']
		, wp_create_nonce('cctm_delete_field')
		, __('Delete this custom field', CCTM_TXTDOMAIN)
		, __('Delete', CCTM_TXTDOMAIN)
	);

	// Show associated post-types
	$d['post_types'] = array();

	if (isset(CCTM::$data['post_type_defs']) && is_array(CCTM::$data['post_type_defs'])) {
		foreach (CCTM::$data['post_type_defs'] as $pt => $pdef) {
			if (isset($pdef['custom_fields']) && is_array($pdef['custom_fields']) 
				&& in_array($field_name, $pdef['custom_fields'])) {
				$d['post_types'][] = $pt;
			}
		}
	}
    
    // Show options -- some different behavior for different types of fields. 
    // TODO: add get_options() as a field to the FormElement class
    // See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=457
    $d['options_desc'] = $FieldObj->get_options_desc();
	
	if (empty($d['post_types'])) {
		$d['post_types'] = '<em>'.__('Unassigned', CCTM_TXTDOMAIN).'</em>';
	}
	else {
		$d['post_types'] = implode(', ', $d['post_types'] );
	}
	

	$data['fields'] .= CCTM::load_view('tr_custom_field.php',$d);
}

$data['content'] = CCTM::load_view('list_custom_fields.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/