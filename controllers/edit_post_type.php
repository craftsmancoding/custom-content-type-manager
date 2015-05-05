<?php
/*------------------------------------------------------------------------------
* Edit an existing post type. 
* @param string $post_type (from $_GET)
*/

if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
require_once(CCTM_PATH.'/includes/CCTM_PostTypeDef.php');

// Variables for our template
$data = array();
$d = array();
if ( isset(CCTM::$data['post_type_defs'][$post_type]) 
	&& (!isset(CCTM::$data['post_type_defs'][$post_type]['is_foreign']) || !CCTM::$data['post_type_defs'][$post_type]['is_foreign']) ) {
	$d['def'] = CCTM::$data['post_type_defs'][$post_type];
	// Older definitions may be missing nodes, so we fill from
	// the default in order to avoid "Undefined index" notices
	foreach(CCTM::$default_post_type_def as $k => $v) {
		if (!isset($d['def'][$k])) {
			$d['def'][$k] = $v;
		}
	}
}
// Oops... bail.
else {
	$data['msg'] = sprintf('<div class="error"><p>%s</p></div>', __('Invalid post_type.', CCTM_TXTDOMAIN));
	$data['page_title']  = __('Unrecognized Content type');
	$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/CreatePostType';
	$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm" title="%s" class="button">%s</a>', __('Back'), __('Back'));
	$data['content'] = '';
	print CCTM::load_view('templates/default.php', $data);
	return;
}

$d['post_type'] = $post_type;
$d['edit_warning'] = sprintf('<br /><span style="color:red;">%s</span>'
	, __('WARNING: changing this value is not recommended.  Changing the post-type name may break permalinks, alter search criteria, and you may have to rename your template files.', CCTM_TXTDOMAIN)
);

$data['page_title']  = __('Edit Content Type: ') . $post_type;
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/CreatePostType';
$fields   = '';
$data['msg'] = '';
$data['menu'] = sprintf('<a href="?page=cctm" title="%s" class="button">%s</a>', __('Cancel'), __('Cancel'));

$d['action_name'] = 'custom_content_type_mgr_edit_content_type';
$d['nonce_name'] = 'custom_content_type_mgr_edit_content_type_nonce';
$d['submit']   = __('Save', CCTM_TXTDOMAIN);

$d['msg']    = '';  // Any validation errors


// Save data if it was properly submitted
if ( !empty($_POST) && check_admin_referer($d['action_name'], $d['nonce_name']) ) {

	$sanitized_vals	= CCTM_PostTypeDef::sanitize_post_type_def($_POST);
	$error_msg 		= CCTM_PostTypeDef::post_type_name_has_errors($sanitized_vals);
	
	if ( empty($error_msg) ) {

		// post_type name was changed (!!!)  This is kinda a big deal.
		// We need to do the following big things:
		// 1. update the post_type column to reflect the new name
		// 2. update the guid  to reflect the new permalink
		// 3. rename the relevant theme files 
		// 4. update the CCTM data definitions
		if ($sanitized_vals['post_type'] != $sanitized_vals['original_post_type_name']) {
			// update the post_type in the database
			global $wpdb;
			$query = $wpdb->prepare("UPDATE {$wpdb->posts} SET post_type=%s WHERE post_type=%s"
				, $sanitized_vals['post_type']
				, $sanitized_vals['original_post_type_name']);
			$wpdb->query($query);
			
			// Update the guid (WARNING: may be time-intensive)
			$query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type=%s", $sanitized_vals['post_type']);
			$myrows = $wpdb->get_results($query, ARRAY_A);
			foreach ($myrows as $r) {
				$wpdb->update($wpdb->posts, array('guid' => get_permalink($r['ID'])), array('ID' => $r['ID']));
			}

			// Merge stuff so we don't obliterate settings such as "is_active" or "custom_fields"
			$sanitized_vals = array_merge(self::$data['post_type_defs'][ $sanitized_vals['original_post_type_name'] ], $sanitized_vals);
			// Out with the old: unset the old option in self::$data;
			unset(self::$data['post_type_defs'][ $sanitized_vals['original_post_type_name'] ]);

			// Try to rename theme file
			$dir = get_stylesheet_directory();
			$oldfilename = $dir . '/single-'.$sanitized_vals['original_post_type_name'].'.php';
			$newfilename = $dir . '/single-'.$sanitized_vals['post_type'].'.php';
			if ( file_exists($oldfilename)) {
				// May generate "Permission denied" warning, so we use @ to suppress it.
				if (!@rename($oldfilename, $dir . '/single-'.$sanitized_vals['post_type'].'.php')) {
					$warning = sprintf( __('You have changed the name of your post_type, so you must also rename your template file! Rename %s to %s.', CCTM_TXTDOMAIN)
						, "<code>$oldfilename</code>"
						, '<code>'.basename($newfilename).'</code>'
					);
					self::register_warning($warning);
				}
			}
		}
		
		CCTM_PostTypeDef::save_post_type_settings($sanitized_vals);
		

		$data['msg'] .= '<div class="updated"><p>'
			. sprintf( __('Settings for %s have been updated.', CCTM_TXTDOMAIN )
			, '<em>'.$sanitized_vals['post_type'].'</em>')
			.'</p></div>';
		self::set_flash($data['msg']);

		print '<script type="text/javascript">window.location.replace("'.get_admin_url(false,'admin.php').'?page=cctm");</script>';
		return;
	}
	else {
		//print $error_msg; exit;
		// clean up... menu labels in particular can get gunked up. :(
		$d['def']  = $sanitized_vals;
		$d['labels']['singular_name'] = '';
		$d['label'] = '';
		$data['msg'] = "<div class='error'><p>$error_msg</p></div>";
	}		
}

$d['icons'] = CCTM_PostTypeDef::get_post_type_icons();
$d['orderby_options'] = CCTM_PostTypeDef::get_orderby_options($post_type);
$d['columns'] = CCTM_PostTypeDef::get_columns($post_type);
$data['content'] = CCTM::load_view('post_type.php', $d);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/