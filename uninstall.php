<?php
/*------------------------------------------------------------------------------
This is run only when this plugin is uninstalled. All cleanup code goes here.

WARNING: uninstalling a plugin fails when developing locally via MAMP et al.
Perhaps related to how WP attempts (and fails) to connect to the local site.
------------------------------------------------------------------------------*/

if ( defined('WP_UNINSTALL_PLUGIN'))
{
	require_once('includes/constants.php');
	require_once('src/CCTM.php');
	require_once('src/FormElement.php');
	
	// If the custom fields modified anything, we need to give them this 
	// opportunity to clean it up.
	$available_custom_field_files = \CCTM\CCTM::get_available_helper_classes('fields');
	foreach ( $available_custom_field_files as $shortname => $file ) {

		include_once($file);
        
		if (class_exists($shortname)){
			$field_type_name = $shortname;
			$FieldObj = new $field_type_name();
			$FieldObj->uninstall();
		}
	}
		
	delete_option('custom_content_types_mgr_data'); // legacy pre 0.9.4
	delete_option('custom_content_types_mgr_settings'); // legacy pre 0.9.4
	delete_option('cctm_data');
	delete_option('cctm_license_key');
	delete_option('cctm_license_status');
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}
/*EOF*/