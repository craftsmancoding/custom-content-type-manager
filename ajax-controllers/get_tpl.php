<?php
/*------------------------------------------------------------------------------
This returns an instance of a tpl for use in a repeatable field.

@param	string	fieldname (with prefix)
@param	integer	instance of the field (has to sync with JS)
------------------------------------------------------------------------------*/
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
// See https://code.google.com/p/wordpress-summarize-posts/issues/detail?id=39
$post_type = CCTM::get_value($_POST, 'post_type', 'post');
$cap = 'edit_posts';
if (isset($GLOBALS['wp_post_types'][$post_type]->cap->edit_posts)) {
	$cap = $GLOBALS['wp_post_types'][$post_type]->cap->edit_posts; 
}
if (!current_user_can($cap)) die('<pre>You do not have permission to do that.</pre>');

require_once(CCTM_PATH.'/includes/CCTM_FormElement.php');

$d = array(); // <-- Template Variables

// Some Tests first to see if the request is valid...
$raw_fieldname = CCTM::get_value($_POST, 'fieldname');
if (empty($raw_fieldname)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($raw_fieldname).'</em>') .'</p>';
	return;
}
$fieldname = preg_replace('/^'. CCTM_FormElement::css_id_prefix . '/', '', $raw_fieldname);

$def = CCTM::get_value(CCTM::$data['custom_field_defs'], $fieldname);
if (empty($def)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($fieldname).'</em>') .'</p>';
	return;
}
$instance = CCTM::get_value($_POST, 'instance');

// Will be either the single or the multi, depending.
$tpl = '';


// Use multi - tpls
if (CCTM::get_value($def,'is_repeatable')) {
	$tpl = CCTM::load_tpl(
		array('fields/elements/'.$def['name'].'.tpl'
			, 'fields/elements/_'.$def['type'].'_multi.tpl'
		)
	);
}
// use normal tpls
else {
	$tpl = CCTM::load_tpl(
		array('fields/elements/'.$def['name'].'.tpl'
			, 'fields/elements/_'.$def['type'].'.tpl'
		)
	);
}

// Just in case...
if (empty($tpl)) {
	print '<p>'.__('Formatting template not found!', CCTM_TXTDOMAIN).'</p>';
	return;	

}

$FieldObj = CCTM::load_object($def['type'], 'fields');
if (!$FieldObj) {
	return;
}
$def['id'] = $fieldname;
$def['i'] = $instance;
$FieldObj->set_props($def);

print CCTM::parse($tpl, $FieldObj->get_props());

/*EOF*/