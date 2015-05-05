<?php
/*------------------------------------------------------------------------------
Manage post_types for the given $field_name.  This is one of two controllers
that provides an access point for editing the associations between post_types
and fields.  This one lets you choose which post_types should contain the 
given custom field.

@param string $field_name
------------------------------------------------------------------------------*/

if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');

$data 				= array();
$data['page_title']	= sprintf( __('Content Types using Custom Field %s', CCTM_TXTDOMAIN), "<em>$field_name</em>" );
$data['help']		= 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/FieldAssociations';
$data['menu'] 		= ''; 
$data['msg']		= CCTM::get_flash();
$data['action_name'] = 'custom_content_type_mgr_delete_field';
$data['nonce_name'] = 'custom_content_type_mgr_delete_field_nonce';
$data['submit']   = __('Save', CCTM_TXTDOMAIN);
$data['cancel_target_url'] = '?page=cctm_fields';
$data['content'] = sprintf('<p>%s</p>'
	, sprintf( __('The following content types are using the %s custom field', CCTM_TXTDOMAIN)
		, "<em>$field_name</em>"
	)
);


// Make sure the field exists
if (!array_key_exists($field_name, self::$data['custom_field_defs'])) {
	$msg_id = 'invalid_field_name';
	include(CCTM_PATH.'/controllers/error.php');
	return;
}

$customized_post_types =  array();
$displayable_types = array();

// this has the side-effect of sorting the post-types
if ( isset(CCTM::$data['post_type_defs']) && !empty(CCTM::$data['post_type_defs']) ) {
	$customized_post_types =  array_keys(CCTM::$data['post_type_defs']);
}
$displayable_types = array_merge(CCTM::$built_in_post_types , $customized_post_types);
$displayable_types = array_unique($displayable_types);


// Save data if it was properly submitted
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	// All associations were removed
	$post_type_defs = CCTM::get_post_type_defs();
	
	if ( !isset($_POST['post_types'])) {
		// die('All associations removed...');
		foreach($displayable_types as $pt) {
			$def = array();
			if ( isset(self::$data['post_type_defs'][$pt])) {
				$def = self::$data['post_type_defs'][$pt];
			}
			
			if (in_array($field_name, $def['custom_fields'])) {
				$revised_custom_fields = array();
				foreach ($def['custom_fields'] as $cf) {
					if ( $cf != $field_name ) {
						$revised_custom_fields[] = $cf;
					}
				}
				self::$data['post_type_defs'][$pt]['custom_fields'] = $revised_custom_fields;
			}
		}
	}
	else {
		foreach($displayable_types as $pt) {
			$def = array();
			if ( isset(self::$data['post_type_defs'][$pt])) {
				$def = self::$data['post_type_defs'][$pt];
			}
			
			// Add the association
			if (in_array($pt, $_POST['post_types'])) {
				if (!isset($def['custom_fields']) 
					|| !is_array($def['custom_fields'])
					|| !in_array($field_name, $def['custom_fields'])) {
					$def['custom_fields'][] = $field_name;
					self::$data['post_type_defs'][$pt]['custom_fields'] = $def['custom_fields'];
				}
			}
			// Remove the association
			else {
				$revised_custom_fields = array();
				if (isset($def['custom_fields'])) {
					foreach ($def['custom_fields'] as $cf) {
						if ( $cf != $field_name ) {
							$revised_custom_fields[] = $cf;
						}
					}
				}
				self::$data['post_type_defs'][$pt]['custom_fields'] = $revised_custom_fields;								
			}
		}
	}
	
	$msg = '<div class="updated"><p>'
		.sprintf( __('The associations for the %s custom field have been updated', CCTM_TXTDOMAIN), "<em>$field_name</em>")
		. '</p></div>';
	self::set_flash($msg);
	include( CCTM_PATH . '/controllers/list_custom_fields.php');
	return;
}


// List Associations
$data['content'] .= '<table>';
foreach ($displayable_types as $post_type) {
	$def = array();
	if (isset(self::$data['post_type_defs'][$post_type])) {
		$def = self::$data['post_type_defs'][$post_type];
	}
	
	if ( in_array($post_type, CCTM::$built_in_post_types) ) {
		$def['description']  = '<img src="'. CCTM_URL .'/images/wp.png" height="16" width="16" alt="wp" /> '. __('Built-in post type.', CCTM_TXTDOMAIN);
	}
	elseif (!isset(self::$data['post_type_defs'][$post_type]['description'])) {
		$def['description'] = '';
	} 
	else {
		$def['description']  = self::$data['post_type_defs'][$post_type]['description'];
	}
	// Images
	$icon = '';
	switch ($post_type) {
	case 'post':
		$icon = '<img src="'. CCTM_URL . '/images/icons/post.png' . '" width="15" height="15"/>';
		break;
	case 'page':
		$icon = '<img src="'. CCTM_URL . '/images/icons/page.png' . '" width="14" height="16"/>';
		break;
	default:
		if ( !empty($def['menu_icon']) && !$def['use_default_menu_icon'] ) {
			$icon = '<img src="'. $def['menu_icon'] . '" />';
		}
		break;
	}
	
	$target_url = sprintf(
		'<a href="?page=cctm&a=list_pt_associations&pt=%s" title="%s">%s</a>'
		, $post_type
		, __('Manage Custom Fields for this content type', CCTM_TXTDOMAIN)
		, __('Manage Custom Fields', CCTM_TXTDOMAIN)
	);

	$is_checked = '';

	if ( isset(self::$data['post_type_defs'][$post_type]['custom_fields']) 
		&& in_array($field_name, self::$data['post_type_defs'][$post_type]['custom_fields'])) {
		$is_checked = ' checked="checked"';
	}
	$data['content'] .= sprintf('
		<tr>
			<td><input type="checkbox" name="post_types[]" id="%s" value="%s" %s/></td>
			<td>%s</td>
			<td><label for="%s" class="cctm_label">%s</label></td>
			<td><span class="cctm_description" style="margin-left:20px;">%s</span><td>
			<td>%s</td>
		</tr>'
		, $post_type
		, $post_type
		, $is_checked
		, $icon
		, $post_type
		, $post_type
		, $def['description']
		, $target_url
	);
	
	// print_r($def); exit;
}

$data['content'] .= '</table>';

$data['content'] = CCTM::load_view('basic_form.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/