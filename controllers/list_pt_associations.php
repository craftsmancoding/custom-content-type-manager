<?php
/**
 * Manage custom fields for the given $post_type.
 *
 * @param string  $post_type
 * @param boolen  $reset     true only if we've just reset all custom fields
 * @package
 */

if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
require_once(CCTM_PATH.'/includes/CCTM_PostTypeDef.php');
require_once(CCTM_PATH.'/includes/CCTM_Metabox.php');

$is_foreign = (int) CCTM::get_value($_GET, 'f');

$data     = array();
$data['page_title'] = sprintf( __('Custom Fields for %s', CCTM_TXTDOMAIN), "<em>$post_type</em>");
$data['help']   = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/FieldAssociations';
$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm&a=create_metabox" class="button">%s</a>', __('Create Metabox', CCTM_TXTDOMAIN) );
$data['msg']  = CCTM::get_flash();


// Validate post type
if (!CCTM_PostTypeDef::is_existing_post_type($post_type) ) {
	$msg_id = 'invalid_post_type';
	include 'error.php';
	return;
}

$data['action_name'] = 'cctm_custom_save_sort_order';
$data['nonce_name'] = 'cctm_custom_save_sort_order_nonce';


// Save custom fields. The sort order is determined by simple physical location on the page.
if (!empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
//	print_r($_POST); exit;
//	print 'Saving!'; exit;
	self::$data['post_type_defs'][$post_type]['custom_fields'] = array();
	
	if (!empty($_POST['mapping']) && is_array($_POST['mapping'])) {
		self::$data['post_type_defs'][$post_type]['custom_fields'] = array();
		self::$data['post_type_defs'][$post_type]['map_field_metabox'] = array();
		foreach ($_POST['mapping'] as $field => $mbox) {
			if (!empty($mbox)) {
				self::$data['post_type_defs'][$post_type]['custom_fields'][] = $field;
				self::$data['post_type_defs'][$post_type]['map_field_metabox'][$field] = $mbox;
			}
		}
	}
	if ($is_foreign){
		self::$data['post_type_defs'][$post_type]['is_foreign'] = 1;
	}
	//print_r(self::$data['post_type_defs'][$post_type]['map_field_metabox']); exit;
	// print '<pre>'.print_r(self::$data,true).'</pre>'; exit;
	update_option( self::db_key, self::$data );
	
	$continue_editing = CCTM::get_value($_POST, 'continue_editing');
	unset($_POST);
	

	$msg = sprintf( __('Custom fields for %s have been updated.', CCTM_TXTDOMAIN), "<em>$post_type</em>" );
	$data['msg'] = sprintf('<div class="updated"><p>%s</p></div>', $msg);
	self::set_flash($data['msg']);

	if (!$continue_editing) {
		include CCTM_PATH . '/controllers/list_post_types.php';
		return;
	}
}

// Active custom fields are those that are associated with THIS post_type
$active_custom_fields = array();
if ( isset(self::$data['post_type_defs'][$post_type]['custom_fields']) ) {
	$active_custom_fields = self::$data['post_type_defs'][$post_type]['custom_fields'];
}
$active_custom_fields_cnt = count($active_custom_fields);

$all_custom_fields = array();
if (isset(self::$data['custom_field_defs']) && is_array(self::$data['custom_field_defs']) ) {
	$all_custom_fields = array_keys(self::$data['custom_field_defs']);
}
$all_custom_fields_cnt = count($all_custom_fields);

if (!$all_custom_fields_cnt) {
	$data['msg'] .= '<div class="updated"><p>'
		. __('There are no custom fields defined yet.', CCTM_TXTDOMAIN)
		.' <a href="'.get_admin_url(false, 'admin.php').'?page=cctm_fields&a=list_custom_field_types">'
		. __('Define custom fields', CCTM_TXTDOMAIN)
		.'</a></p></div>';
}
elseif (!$active_custom_fields_cnt ) {
	$data['msg'] .= sprintf('<div class="updated"><p>%s</p></div>'
		, sprintf( __('The %s post type does not have any custom fields yet.', CCTM_TXTDOMAIN)
			, "<em>$post_type</em>" ));
}

$data['content'] = '';
$data['unused'] = '';
$data['advanced_boxes'] = '';
$data['normal_boxes'] = '';
$data['side_boxes'] = '';

// Load up this structure
$metaboxes = array();

// Gather and order the custom fields active for this post_type
foreach ($active_custom_fields as $cf) {
	if ( !isset(self::$data['custom_field_defs'][$cf])) {
		continue;
	}
	$d = self::$data['custom_field_defs'][$cf];

	if (!$FieldObj = CCTM::load_object($d['type'], 'fields')) {
		continue;
	}
	$metabox = 'cctm_default';
	if (isset(self::$data['post_type_defs'][$post_type]['map_field_metabox'][$cf])) {
		$metabox = self::$data['post_type_defs'][$post_type]['map_field_metabox'][$cf];
	}
//print_r(self::$data['metabox_defs']); exit;
	// Default metabox def
	$m_def = CCTM::$metabox_def;
	
	if (isset(self::$data['metabox_defs'][$metabox])) {
		$m_def = self::$data['metabox_defs'][$metabox];
	}
	$context = $m_def['context'];
	
	$d['icon'] = $FieldObj->get_icon();
	if ( !CCTM::is_valid_img($d['icon']) ) {
		$d['icon'] = self::get_custom_icons_src_dir() . 'default.png';
	}
	$d['class'] = 'ui-state-highlight';
	$d['metabox'] = $metabox;

	$d['edit_field_link'] = sprintf(
		'<a href="%s/wp-admin/admin.php?page=cctm_fields&a=edit_custom_field&field=%s&_wpnonce=%s" title="%s" class="linklike">%s</a>'
		, get_site_url()
		, $d['name']
		, wp_create_nonce('cctm_edit_field')
		, __('Edit this custom field', CCTM_TXTDOMAIN)
		, __('Edit', CCTM_TXTDOMAIN)
	);

	$metaboxes[$context][$metabox][] = CCTM::load_view('li_field.php', $d);

}
//print_r(self::$data['metabox_defs']); exit;
//print_r(self::$data['post_type_defs'][$post_type]['map_field_metabox']);
// Get the metaboxes with no custom-fields in them
if (isset(self::$data['metabox_defs']) && is_array(self::$data['metabox_defs'])) {
	foreach (self::$data['metabox_defs'] as $mb_id => $mb_def) {
		if (!isset($metaboxes[ $mb_def['context'] ][$mb_id])) {
			$metaboxes[ $mb_def['context'] ][$mb_id] = array();	
		}
	}
}

// Get things sorted into their spots
if (isset($metaboxes['normal'])) {
	foreach($metaboxes['normal'] as $m => $items) {
		$data['normal_boxes'] .= CCTM_Metabox::get_metabox_holder($m,$items);
	}
}
if (isset($metaboxes['advanced'])) {
	foreach($metaboxes['advanced'] as $m => $items) {
		$data['advanced_boxes'] .= CCTM_Metabox::get_metabox_holder($m,$items);
	}
}
if (isset($metaboxes['side'])) {
	foreach($metaboxes['side'] as $m => $items) {
		$data['side_boxes'] .= CCTM_Metabox::get_metabox_holder($m,$items);
	}
}
//print_r($data); exit;


// List the unused custom fields
$remaining_custom_fields = array_diff($all_custom_fields, $active_custom_fields);

foreach ($remaining_custom_fields as $cf) {
	$d = self::$data['custom_field_defs'][$cf];

	if(!$FieldObj = CCTM::load_object($d['type'],'fields')) {
		continue;
	}
	
	$d['icon'] = $FieldObj->get_icon();

	if ( !CCTM::is_valid_img($d['icon']) ) {
		$d['icon'] = self::get_custom_icons_src_dir() . 'default.png';
	}

	$d['class'] = 'ui-state-default';
	$d['metabox'] = '';

	$d['edit_field_link'] = sprintf(
		'<a href="%s/wp-admin/admin.php?page=cctm_fields&a=edit_custom_field&field=%s&_wpnonce=%s" title="%s" class="linklike">%s</a>'
		, get_site_url()
		, $d['name']
		, wp_create_nonce('cctm_edit_field')
		, __('Edit this custom field', CCTM_TXTDOMAIN)
		, __('Edit', CCTM_TXTDOMAIN)
	);

	$data['unused'] .= CCTM::load_view('li_field.php', $d);
}

$data['content'] = CCTM::load_view('metaboxes.php', $data);
print CCTM::load_view('templates/default.php', $data);


/*EOF*/