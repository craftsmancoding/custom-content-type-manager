<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
//------------------------------------------------------------------------------
/**
 * Show all available types of Custom Fields
 *
 */
//print '<pre>'; print_r(CCTM::$data['cache']); print '</pre>';
$data=array();
$data['page_title'] = __('Add Field: Choose Type of Custom Field', CCTM_TXTDOMAIN);
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/SupportedCustomFields';
$data['msg'] = self::get_flash();
$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_fields&a=list_custom_fields" class="button">%s</a>', __('Back', CCTM_TXTDOMAIN) );
$data['fields'] = '';
$data['content'] = '';

// You can optionally create the field for a given post_type
if(!empty($post_type)) {
	$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_fields&a=list_post_types" class="button">%s</a>', __('Back', CCTM_TXTDOMAIN) );
	$data['content'] .= '<p>'. sprintf(__('Create a custom field for the %s post_type', CCTM_TXTDOMAIN), "<em>$post_type</em>").'</p>';
}

$elements = CCTM::get_available_helper_classes('fields');
//print_r($elements); exit; // EHG 
foreach ( $elements as $field_type => $file ) {
	if ($FieldObj = CCTM::load_object($field_type,'fields') ) {
		$d = array();		
		$d['name'] 			= $FieldObj->get_name();
		$d['icon'] 			= $FieldObj->get_icon();
		$d['description']	= $FieldObj->get_description();
		$d['url'] 			= $FieldObj->get_url();
		$d['type'] 			= $field_type;
		$d['post_type']		= $post_type;
		
		$data['fields'] .= CCTM::load_view('tr_custom_field_type.php',$d);	
	}
	else {
		$data['fields'] .= sprintf(
			__('Form element not found: %s', CCTM_TXTDOMAIN)
			, "<code>$field_type</code>"
		);
	}

}

$data['content'] .= CCTM::load_view('custom_field_types.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/