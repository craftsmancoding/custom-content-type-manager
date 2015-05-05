<?php
/*------------------------------------------------------------------------------
This is run on the admin_init event: this is what generates the menus in 
the WP dashboard.  The default behavior here makes the menu show only for 
site administrators or for super_admin's (in a multi-site install).

DO NOT OVERWRITE THIS FILE DIRECTLY!  Instead, create a copy of this file inside
wp-content/uploads/cctm/menus/ -- this ensures that your
custom modications are preserved in a place that will not be overwritten by the 
WordPress update process.
------------------------------------------------------------------------------*/

// Adjust menus for multi-site: menu should only be visible to the super_admin
$capability = 'manage_options';

if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE == true && is_super_admin()) {
//	$capability = 'manage_network'; // <-- this doesn't work in 3.3.1. WTF???
	$capability = 'manage_options';
}

$active_post_types = self::get_active_post_types();

// Main menu item
add_menu_page(
	__('Manage Custom Content Types', CCTM_TXTDOMAIN),  // page title
	__('Custom Content Types', CCTM_TXTDOMAIN),      // menu title
	$capability,						// capability
	'cctm',								// menu-slug (should be unique)
	'CCTM::page_main_controller',       // callback function
	CCTM_URL .'/images/gear.png',       // Icon
	self::menu_position					// menu position
);

add_submenu_page(
	'cctm',          // parent slug (menu-slug from add_menu_page call)
	__('CCTM Custom Fields', CCTM_TXTDOMAIN),  // page title
	__('Custom Fields', CCTM_TXTDOMAIN),   // menu title
	$capability,						// capability
	'cctm_fields',						// menu_slug: cf = custom fields
	'CCTM::page_main_controller'		// callback function
);

add_submenu_page(
	'cctm',         // parent slug (menu-slug from add_menu_page call)
	__('CCTM Global Settings', CCTM_TXTDOMAIN),  // page title
	__('Global Settings', CCTM_TXTDOMAIN),	// menu title
	$capability,							// capability
	'cctm_settings',						// menu_slug
	'CCTM::page_main_controller'			// callback function
);

add_submenu_page(
	'cctm',         // parent slug (menu-slug from add_menu_page call)
	__('CCTM Tools', CCTM_TXTDOMAIN),   // page title
	__('Tools', CCTM_TXTDOMAIN),    // menu title
	$capability,					// capability
	'cctm_tools',					// menu_slug
	'CCTM::page_main_controller'	// callback function
);

add_submenu_page(
	'cctm',         // parent slug (menu-slug from add_menu_page call)
	__('CCTM Clear Cache', CCTM_TXTDOMAIN),   // page title
	__('Clear Cache', CCTM_TXTDOMAIN),    // menu title
	$capability,					// capability
	'cctm_cache',					// menu_slug
	'CCTM::page_main_controller'	// callback function
);

// Add Custom Fields links to each post type
if (self::get_setting('show_custom_fields_menu')) {
	foreach ($active_post_types as $post_type) {
		$parent_slug = 'edit.php?post_type='.$post_type;
		if ($post_type == 'post') {
			$parent_slug = 'edit.php';
		}
		add_submenu_page(
			$parent_slug
			, __('Custom Fields', CCTM_TXTDOMAIN)
			, __('Custom Fields', CCTM_TXTDOMAIN)
			, $capability
			, 'cctm&a=list_pt_associations&pt='.$post_type
			, 'CCTM::page_main_controller'
		);
	}
}

// Add Settings links to each post type
if (self::get_setting('show_settings_menu')) {
	foreach ($active_post_types as $post_type) {
		$parent_slug = 'edit.php?post_type='.$post_type;
		if ( in_array($post_type, self::$reserved_post_types) ) {
			continue;
		}
		add_submenu_page(
			$parent_slug
			, __('Settings', CCTM_TXTDOMAIN)
			, __('Settings', CCTM_TXTDOMAIN)
			, $capability
			, 'cctm&a=edit_post_type&pt='.$post_type
			, 'CCTM::page_main_controller'
		);
	}
}

// Remove any menus that the user has selected to hide under the Global Settings
global $menu;

$remove_me = array();
if(self::get_setting('hide_posts')) {
	$remove_me[] = __('Posts');
}
if(self::get_setting('hide_pages')) {
	$remove_me[] = __('Pages');
}
if(self::get_setting('hide_links')) {
	$remove_me[] = __('Links');
}
if(self::get_setting('hide_comments')) {
	$remove_me[] = __('Comments');
}
if (!empty($remove_me)) {
	foreach ($menu as $k => $v) {
		foreach ($remove_me as $menu_item) {
			// There is no unique id (*facepalm*), and the 0th position may include html <spans> (*facepalm again*)
			if (!strncmp($v[0], $menu_item, strlen($menu_item))) {
				unset($menu[$k]);
				break; // on to the next
			}		
		}
	}
}



/*EOF*/
