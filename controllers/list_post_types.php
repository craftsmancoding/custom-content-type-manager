<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Lists all defined post types:
Post-types come in 3 flavors w 3 formatting variations:
	1. Built-in post-types whose custom fields may be managed: posts, pages
	2. Post-Types for which the CCTM can have full control over (this includes
		both active post-types and post-types which have full definitions for)
	3. "Foreign" post-types registered by some other plugin whose custom fields
		the CCTM may standardize upon request.
------------------------------------------------------------------------------*/
$data 				= array();
$data['page_title']	= __('List Content Types', CCTM_TXTDOMAIN);
$data['menu'] 		= sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm&a=create_post_type" class="button">%s</a>', __('Create Content Type', CCTM_TXTDOMAIN) );
$data['msg']		= CCTM::get_flash();
$data['row_data'] = '';


$all_types = CCTM::get_post_types();

foreach ( $all_types as $post_type ) {	

	
	$hash = array(); // populated for the tpl
	$hash['post_type'] = $post_type;
	$hash['icon'] = '';
	
	// Get our default links
	$deactivate    = sprintf(
			'<a href="?page=cctm&a=deactivate_post_type&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('Deactivate this content type', CCTM_TXTDOMAIN)
			, __('Deactivate', CCTM_TXTDOMAIN)
		);
	$edit_link     = sprintf(
			'<a href="?page=cctm&a=edit_post_type&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('Edit this content type', CCTM_TXTDOMAIN )
			, __('Edit', CCTM_TXTDOMAIN)
		);

	$duplicate_link     = sprintf(
			'<a href="?page=cctm&a=duplicate_post_type&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('Duplicate this content type', CCTM_TXTDOMAIN )
			, __('Duplicate', CCTM_TXTDOMAIN)
		);

	$manage_custom_fields  = sprintf(
			'<a href="?page=cctm&a=list_pt_associations&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('Manage Custom Fields for this content type', CCTM_TXTDOMAIN)
			, __('Manage Custom Fields', CCTM_TXTDOMAIN)
		);
	$view_templates   = sprintf('<a href="?page=cctm&a=template_single&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('View Sample Templates for this content type', CCTM_TXTDOMAIN )
			, __('View Sample Templates', CCTM_TXTDOMAIN)
		);
	
	//------------------------------------------------------------------------------
	// post,page: Built-in post types
	//------------------------------------------------------------------------------
	if ( in_array($post_type, CCTM::$built_in_post_types) ) {
		$deactivate    = sprintf(
			'<a href="?page=cctm&a=deactivate_post_type&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('Stop standardizing custom fields this content type', CCTM_TXTDOMAIN)
			, __('Release Custom Fields', CCTM_TXTDOMAIN)
		);
		$hash['description']  = __('Built-in post type.', CCTM_TXTDOMAIN);
		$hash['edit_manage_view_links'] = '<img src="'. CCTM_URL .'/images/wp.png" height="16" width="16" alt="wp"/> ' . $manage_custom_fields . ' | ' . $view_templates;
		// Not active
		if (!isset(CCTM::$data['post_type_defs'][$post_type]['is_active']) || !CCTM::$data['post_type_defs'][$post_type]['is_active']) {
			$hash['class'] = 'inactive';
			$hash['activate_deactivate_delete_links'] = '<span class="activate">'
				. sprintf(
					'<a href="?page=cctm&a=activate_post_type&pt=%s" title="%s">%s</a>'
					, $post_type
					, __('Standardize Custom Fields for this content type', CCTM_TXTDOMAIN)
					, __('Standardize Custom Fields', CCTM_TXTDOMAIN)
				) . '</span>';
		}
		// Active
		else {
			$hash['class'] = 'active';
			$hash['activate_deactivate_delete_links'] = sprintf(
				'<a href="?page=cctm&a=deactivate_post_type&pt=%s" title="%s">%s</a>'
				, $post_type
				, __('Stop standardizing custom fields this content type', CCTM_TXTDOMAIN)
				, __('Release Custom Fields', CCTM_TXTDOMAIN)
			);
		}
		
	
		if('page' == $post_type) {
			$hash['icon'] = '<img src="'. CCTM_URL . '/images/icons/page.png' . '" width="14" height="16"/>';
		}
		else {
			$hash['icon'] = '<img src="'. CCTM_URL . '/images/icons/post.png' . '" width="15" height="15"/>';
		}
		
		$data['row_data'] .= CCTM::load_view('tr_post_type.php', $hash);
	}
	//------------------------------------------------------------------------------
	// Full fledged CCTM post-types
	//------------------------------------------------------------------------------
	elseif (isset(CCTM::$data['post_type_defs'][$post_type]['post_type'])) {
		$hash['description']  = CCTM::get_value(CCTM::$data['post_type_defs'][$post_type], 'description');
		$hash['edit_manage_view_links'] = $edit_link . ' | '. $manage_custom_fields . ' | ' . $view_templates . ' | ' . $duplicate_link;

		if ( isset(CCTM::$data['post_type_defs'][$post_type]['is_active']) && !empty(CCTM::$data['post_type_defs'][$post_type]['is_active']) ) {
	
			$hash['class'] = 'active';
			$hash['activate_deactivate_delete_links'] = '<span class="deactivate">'.$deactivate.'</span>';
			$is_active = true;
		}
		else {
			$hash['class'] = 'inactive';
			$hash['activate_deactivate_delete_links'] = '<span class="activate">'
				. sprintf(
					'<a href="?page=cctm&a=activate_post_type&pt=%s" title="%s">%s</a>'
					, $post_type
					, __('Activate this content type', CCTM_TXTDOMAIN)
					, __('Activate', CCTM_TXTDOMAIN)
				) . ' | </span>'
				. '<span class="delete">'. sprintf(
				'<a href="?page=cctm&a=delete_post_type&pt=%s" title="%s">%s</a>'
					, $post_type
					, __('Delete this content type', CCTM_TXTDOMAIN)
					, __('Delete', CCTM_TXTDOMAIN)
				).'</span>';
			$is_active = false;
		}
		if ( !empty(CCTM::$data['post_type_defs'][$post_type]['menu_icon']) && !CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon'] ) {
			$hash['icon'] = '<img src="'. CCTM::$data['post_type_defs'][$post_type]['menu_icon'] . '" />';
		}
		
		$data['row_data'] .= CCTM::load_view('tr_post_type.php', $hash);	
	}
	//------------------------------------------------------------------------------
	// Foreign post-types
	//------------------------------------------------------------------------------
	elseif(self::get_setting('show_foreign_post_types')) {
		$hash['description']  = __('Foreign post-type.  This post type has been registered by another plugin.', CCTM_TXTDOMAIN);
		$hash['edit_manage_view_links'] = '<img src="'. CCTM_URL .'/images/spy.png" height="16" width="16" alt="wp"/> ' . $manage_custom_fields . ' | ' . $view_templates;
		// Not active
		if (!isset(CCTM::$data['post_type_defs'][$post_type]['is_active']) || !CCTM::$data['post_type_defs'][$post_type]['is_active']) {
			$hash['class'] = 'inactive';
			$hash['activate_deactivate_delete_links'] = '<span class="activate">'
				. sprintf(
					'<a href="?page=cctm&a=activate_post_type&pt=%s" title="%s">%s</a>'
					, $post_type
					, __('Standardize Custom Fields for this content type', CCTM_TXTDOMAIN)
					, __('Standardize Custom Fields', CCTM_TXTDOMAIN)
				) . '</span>';
		}
		// Active
		else {
			$hash['class'] = 'active';
			$hash['activate_deactivate_delete_links'] = sprintf(
				'<a href="?page=cctm&a=deactivate_post_type&pt=%s" title="%s">%s</a>'
				, $post_type
				, __('Stop standardizing custom fields this content type', CCTM_TXTDOMAIN)
				, __('Release Custom Fields', CCTM_TXTDOMAIN)
			);
		}
		$hash['icon'] = '<img src="'. CCTM_URL . '/images/forbidden.png' . '" width="16" height="16"/>';
		
		$data['row_data'] .= CCTM::load_view('tr_post_type.php', $hash);
	}
}


$data['content'] = CCTM::load_view('list_post_types.php', $data);
print CCTM::load_view('templates/default.php', $data);
