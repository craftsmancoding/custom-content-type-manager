<?php
/*------------------------------------------------------------------------------
This is run only when this plugin is uninstalled. All cleanup code goes here.

WARNING: uninstalling a plugin fails when developing locally via MAMP et al.
Perhaps related to how WP attempts (and fails) to connect to the local site.
------------------------------------------------------------------------------*/

if ( defined('WP_UNINSTALL_PLUGIN'))
{
	
	delete_option('cctm_data');
	delete_option('custom_content_types_mgr_data'); // legacy pre 0.9.4
	delete_option('custom_content_types_mgr_settings'); // legacy pre 0.9.4
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}
/*EOF*/