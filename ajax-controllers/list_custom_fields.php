<?php
/*------------------------------------------------------------------------------
This lists available custom fields so the user can easily choose one to insert
the corresponding [custom_field] shortcode
Remember: the output here MUST be wrapped in HTML tags, otherwise jQuery's .html()
method will kack.
------------------------------------------------------------------------------*/
if (!defined('CCTM_PATH')) exit('No direct script access allowed');

$post_type = CCTM::get_value($_POST, 'post_type');

if (empty($post_type)) {
	printf('<div class="error"><table><tr><td><img src="%s/images/warning-icon.png" height="44" width="50"/></td><td><p>%s</p></td></tr></table></div>', CCTM_URL, __('No post-type detected. Cannot display custom fields.', CCTM_TXTDOMAIN));
	return;
}

if (!isset(CCTM::$data['post_type_defs'][$post_type]['is_active']) || !CCTM::$data['post_type_defs'][$post_type]['is_active']) {
	printf('<div class="error"><table><tr><td><img src="%s/images/warning-icon.png" height="44" width="50"/></td><td><p>%s <a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/custom_field_shortcode"><img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" /></a></p></td></tr></table></div>', CCTM_URL, __('The custom fields for this post-type are not standardized. You can manually type in the shortcode to print the custom field value from another post using the following format:  <code>[custom_field name="name_of_field" filter="optional_output_filter" post_id="123"]</code>', CCTM_TXTDOMAIN));
	return;
}

// Template variables
$d = array();
$d['msg'] = '';
$d['page_title'] = __('Custom Fields', CCTM_TXTDOMAIN);
$d['content'] = '';


$custom_fields = array();

if (isset(CCTM::$data['post_type_defs'][$post_type]['custom_fields'])) {
	$custom_fields = CCTM::$data['post_type_defs'][$post_type]['custom_fields'];
}

if (empty($custom_fields)) {
	$d['content'] = '<div class="error"><p>'.__('This post-type does not have any custom fields associated with it.', CCTM_TXTDOMAIN).'</p></div>';
}


$d['content'] .= '<ul>';
foreach($custom_fields as $cf) {

	if (!isset(CCTM::$data['custom_field_defs'][$cf])) {
		continue;
	}
	$def = CCTM::$data['custom_field_defs'][$cf];
	
	$shortcode = sprintf('[custom_field name="%s"]', $def['name']);
	
	$d['content'] .= sprintf('<li>
		<strong class="linklike" onclick="javascript:insert_shortcode(\'%s\');">%s</strong> 
		: <span>%s</span></li>'
		, htmlspecialchars(addslashes($shortcode))
		, $def['label']
		, $def['description']
	);

}
$d['content'] .= '</ul>';


print CCTM::load_view('templates/tinymce.php', $d);
/*EOF*/