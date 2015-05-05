<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Change the type of a custom field, e.g. from text to textarea.

@param string $field_name	uniquely identifies this field
------------------------------------------------------------------------------*/
$field_name = CCTM::get_value($_GET, 'field');

// Make sure the field exists
if (!array_key_exists($field_name, self::$data['custom_field_defs'])) {
	$msg_id = 'invalid_field_name';
	include(CCTM_PATH.'/controllers/error.php');
	return;
}


// Page variables
$data = array();
$data['page_title'] = sprintf(__('Change Field Type: %s', CCTM_TXTDOMAIN), $field_name );
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/ChangeFieldType';
$data['msg'] = '';
$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_fields&a=list_custom_fields" title="%s" class="button">%s</a>', __('Cancel'), __('Cancel'));
$data['submit'] = __('Save', CCTM_TXTDOMAIN);

$data['content'] = '';
$data['action_name']  = 'custom_content_type_mgr_edit_custom_field';
$data['nonce_name']  = 'custom_content_type_mgr_edit_custom_field_nonce';

$nonce = self::get_value($_GET, '_wpnonce');
if (! wp_verify_nonce($nonce, 'cctm_change_field_type') ) {
	die( __('Invalid request.', CCTM_TXTDOMAIN ) );
}
		
	
// Save if submitted...
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	$new_field_type = CCTM::get_value($_POST,'new_field_type');
	$old_field_type = CCTM::get_value($_POST,'old_field_type');
	
	if ($FieldObj = CCTM::load_object($new_field_type,'fields') ) {
		$field_type_str = $FieldObj->get_name();
		$field_type_url = $FieldObj->get_url();
		
		self::$data['custom_field_defs'][$field_name]['type'] = $new_field_type;
		
		update_option( self::db_key, self::$data );
		$data['msg'] = sprintf('<div class="updated"><p>%s</p></div>'
				, sprintf(__('The %s custom field has been converted to a %s field.', CCTM_TXTDOMAIN)
				, '<em>'.$field_name.'</em>', $field_type_str));		
		self::set_flash($data['msg']);
		include(CCTM_PATH.'/controllers/list_custom_fields.php');
		return;
	}
	else {
		die(__('There was a problem converting the field type.', CCTM_TXTDOMAIN));
	}
}

$field_type = self::$data['custom_field_defs'][$field_name]['type'];
$field_data = self::$data['custom_field_defs'][$field_name]; // Data object we will save

$field_type_str = '';
if ($FieldObj = CCTM::load_object($field_type,'fields')) {
	$field_type_str = $FieldObj->get_name();
	$field_type_url = $FieldObj->get_url();
}

$data['content'] = '<p>'.sprintf(__('Change the %s field from a %s field into the following type of field:', CCTM_TXTDOMAIN), '<code>'.$field_data['name'].'</code>', sprintf('<a href="%s">%s</a>', $field_type_url, $field_type_str) ) . '</p>';

$elements = CCTM::get_available_helper_classes('fields');
$data['content'] .= '<input type="hidden" name="old_field_type" value="'.$field_type.'">';
$data['content'] .= '<select name="new_field_type" id="new_field_type">';
foreach ( $elements as $ft => $file ) {
	if ($field_type == $ft) {
		continue; //  can't  change a field to itself
	}
	if ($FieldObj = CCTM::load_object($ft,'fields')) {
		$d = array();		
		$data['content'] .= sprintf('<option value="%s">%s</option>', $ft, $FieldObj->get_name());
	}
	else {
		die( sprintf(__('Form element not found: %s', CCTM_TXTDOMAIN)
			, "<code>$field_type</code>") );
	}

}
$data['content'] .= '</select>';

$data['content'] .= '<p style="color:red;">'.__('WARNING: different fields have different attributes. The conversion process may cause some attributes in your field definition to be lost.  Export a copy of your field definitions before continuing.', CCTM_TXTDOMAIN). '</p>';

$data['content'] .= '<p style="color:red;">'.sprintf('<a href="%s">%s</a>', '?page=cctm_tools&a=export_def' ,__('Export a copy of your field definitions before continuing.', CCTM_TXTDOMAIN)). '</p>';


$data['content'] = CCTM::load_view('basic_form.php', $data);
print CCTM::load_view('templates/default.php', $data);


/*EOF*/