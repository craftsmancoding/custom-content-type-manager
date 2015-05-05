<?php
/*------------------------------------------------------------------------------
Settings Page:

Lets users configure global options.

------------------------------------------------------------------------------*/
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
require_once(CCTM_PATH.'/includes/CCTM_FormElement.php');

$data 				= array();
$data['page_title']	= __('Settings', CCTM_TXTDOMAIN);
$data['help'] 		= 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Settings';
$data['menu'] 		='';
$data['msg']		= self::get_flash();
$data['action_name']  = 'custom_content_type_mgr_settings';
$data['nonce_name']  = 'custom_content_type_mgr_settings';
$data['submit']   = __('Save', CCTM_TXTDOMAIN);
$data['custom_fields_settings_links'] = ''; // <-- optionally kicks in if the Field Element implements the get_settings_page() function

// Add links to any custom field settings here
$data['content'] = ''; 

// If properly submitted, Proceed with deleting the post type
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	self::$data['settings']['delete_posts'] 			= (int) CCTM::get_value($_POST, 'delete_posts', 0);	
	self::$data['settings']['delete_custom_fields'] 	= (int) CCTM::get_value($_POST, 'delete_custom_fields', 0);
	self::$data['settings']['add_custom_fields'] 		= (int) CCTM::get_value($_POST, 'add_custom_fields', 0);
	self::$data['settings']['show_custom_fields_menu']	= (int) CCTM::get_value($_POST, 'show_custom_fields_menu', 0);
	self::$data['settings']['show_settings_menu'] 		= (int) CCTM::get_value($_POST, 'show_settings_menu', 0);
	self::$data['settings']['show_foreign_post_types'] 	= (int) CCTM::get_value($_POST, 'show_foreign_post_types', 0);
	self::$data['settings']['cache_thumbnail_images'] 	= (int) CCTM::get_value($_POST, 'cache_thumbnail_images', 0);
	self::$data['settings']['save_empty_fields'] 		= (int) CCTM::get_value($_POST, 'save_empty_fields', 0);
	self::$data['settings']['summarizeposts_tinymce'] 	= (int) CCTM::get_value($_POST, 'summarizeposts_tinymce', 0);
	self::$data['settings']['custom_fields_tinymce'] 	= (int) CCTM::get_value($_POST, 'custom_fields_tinymce', 0);
	self::$data['settings']['pages_in_rss_feed'] 		= (int) CCTM::get_value($_POST, 'pages_in_rss_feed', 0);	
	self::$data['settings']['enable_right_now'] 		= (int) CCTM::get_value($_POST, 'enable_right_now', 0);	
 	
 	self::$data['settings']['hide_posts'] 				= (int) CCTM::get_value($_POST, 'hide_posts', 0);	
 	self::$data['settings']['hide_pages'] 				= (int) CCTM::get_value($_POST, 'hide_pages', 0);	
 	self::$data['settings']['hide_links'] 				= (int) CCTM::get_value($_POST, 'hide_links', 0);	
 	self::$data['settings']['hide_comments'] 			= (int) CCTM::get_value($_POST, 'hide_comments', 0);	
 	
	update_option( self::db_key, self::$data );

	$data['msg'] = '<div class="updated"><p>'
		. __('Settings have been updated.', CCTM_TXTDOMAIN )
		.'</p></div>';
	self::set_flash($data['msg']);
	print '<script type="text/javascript">window.location.replace("?page=cctm_settings");</script>';
	return;
}

// Use Defaults by default...
$data['settings'] = CCTM::$default_settings;

// list all checkboxes here
$checkboxes = array(
	'delete_posts' 
	, 'delete_custom_fields'
	, 'add_custom_fields'
 	, 'show_custom_fields_menu'
 	, 'show_settings_menu'
 	, 'show_foreign_post_types'
 	, 'cache_directory_scans'
 	, 'cache_thumbnail_images'
 	, 'save_empty_fields'
 	, 'summarizeposts_tinymce'
 	, 'custom_fields_tinymce'
 	, 'pages_in_rss_feed'
 	, 'enable_right_now'
 	, 'hide_posts'
 	, 'hide_pages'
 	, 'hide_links'
 	, 'hide_comments'
);

// this only works for checkboxes...
foreach ( $checkboxes as $k) {
	if (self::get_setting($k)) {
		$data['settings'][$k] = ' checked="checked"';
	}
}

// Load up any settings pages for custom fields
$element_files = CCTM::get_available_helper_classes('fields');
$flag = false;
foreach ( $element_files as $shortname => $file ) {
	require_once($file);

	if ( class_exists(CCTM::filter_prefix.$shortname) ) {
		$d = array();
		$field_type_name = CCTM::filter_prefix.$shortname;
		$FieldObj = new $field_type_name();
		
		if ($FieldObj->get_settings_page() ) {
			$flag = true;
			$data['custom_fields_settings_links'] .= sprintf(
				'<li><strong>%s</strong>: %s (<a href="?page=cctm_settings&a=settings_cf&type=%s">%s</a>)'
				, $FieldObj->get_name()
				, $FieldObj->get_description()
				, $shortname
				, __('Edit Settings', CCTM_TXTDOMAIN)
			);
			
		}
	}
}
// We gots some custom settings for custom fields!
if ($flag) {
	$data['custom_fields_settings_links'] = '<h3>'.__('Custom Fields', CCTM_TXTDOMAIN).'</h3>
		<ul>'. $data['custom_fields_settings_links'] . '</ul>';
}

$data['content'] .= CCTM::load_view('settings.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/