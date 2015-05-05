<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Merge one custom field definition into another.  This isn't intelligent about
its merging: if you merge "cats" into "dogs", any custom field named "cats" 
will be renamed to "dogs" in the wp_postmeta table, and the definition for 
"cats" will simply be deleted.

------------------------------------------------------------------------------*/

// Variables for our template
$data['page_title']  = __('Merge Custom Field', CCTM_TXTDOMAIN) . " <em>$field_name</em>";
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/MergeCustomField';
$data['msg'] = ''; // Any validation errors
$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_fields&a=list_custom_fields" title="%s" class="button">%s</a>', __('Cancel'), __('Cancel'));

$d['action_name'] = 'custom_content_type_mgr_merge_fields';
$d['nonce_name'] = 'custom_content_type_mgr_merge_fields';
$d['submit']   = __('Merge', CCTM_TXTDOMAIN);




// Save data if it was properly submitted
if ( !empty($_POST) && check_admin_referer($d['action_name'], $d['nonce_name'])) {
	// Error?
	if (isset($_POST['merge_target']) && empty($_POST['merge_target'])) {
		$data['msg']    = sprintf('<div class="error"><p>%s</p></div>'
			, __('You must select a target to merge the field into.', CCTM_TXTDOMAIN)
		);
	}
	// Merge
	else {
	//	die('merging...');
		global $wpdb;
		$query = $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_key='target_value' WHERE meta_key=%s;"
			, $_POST['merge_target']
			, $field_name);
		$wpdb->query($query);
		
		// pool the post_type associations: 
		// Condition we look for: 
		// 	--	The old field_name is associated with post_type X, 
		//  	make sure that new merge_target fieldname is also associated with X
		foreach (self::$data['post_type_defs'] as $pt => $def) {
			if (isset(self::$data['post_type_defs'][$pt]['custom_fields']) && is_array(self::$data['post_type_defs'][$pt]['custom_fields'])) {
	
				if (in_array($field_name, self::$data['post_type_defs'][$pt]['custom_fields'])) {

					// remove the old field's association
					$revised_fields = array();
					foreach(self::$data['post_type_defs'][$pt]['custom_fields'] as $field) {
						if ($field != $field_name) {
							$revised_fields[] = $field;
						}
					}
					// Add the target field if it's not there already
					if (!in_array($_POST['merge_target'], self::$data['post_type_defs'][$pt]['custom_fields'])) {
						$revised_fields[] = $_POST['merge_target'];
					}
					
					self::$data['post_type_defs'][$pt]['custom_fields'] = $revised_fields;
				}
			}
		}

		// unset the old field in self::$data;
		unset(self::$data['custom_field_defs'][$field_name]);	
		update_option(self::db_key, self::$data); // <--- finally, save it all
		
		$msg = '<div class="updated"><p>'
			. sprintf( __('The %s field has been merged into the %s field.', CCTM_TXTDOMAIN )
				, '<em>'.$field_name.'</em>'
				, '<em>'.$_POST['merge_target'].'</em>'
			)
			.'</p></div>';
			
		self::set_flash($msg);
		print '<script type="text/javascript">window.location.replace("?page=cctm_fields");</script>';
		return;
	}
}

$d['content'] = '';

$d['content'] .= '<p>';
$d['content'] .= __('Merging causes a field to be renamed in the database and the old definition to be deleted. For example, if you merge "cats" into "dogs", all instances of "cats" in the database will be renamed to "dogs" and the field definition for "cats" will be removed.', CCTM_TXTDOMAIN);
$d['content'] .= '</p>';
$d['content'] .= '<p><strong>' . __('Be careful about merging a field into a field of a different type! You may encounter unpredictable results!')	. '</strong></p>';

$d['content'] .= '<p>'.sprintf( __('Choose a custom field below that will absorb the values for the %s field.', CCTM_TXTDOMAIN)
	, "<strong><em>$field_name</em></strong>" ) .'</p>';


$d['content'] .= '<select name="merge_target">
	<option value="">'.__('Choose target', CCTM_TXTDOMAIN) .'</option>';
foreach ( self::$data['custom_field_defs'] as $fieldname => $def) {
	// Skip THIS field as a viable target for the merge. $field_name is the name of the field being merged
	if ($fieldname != $field_name) {
		$d['content'] .= sprintf('<option value="%s">%s (%s) : %s</option>'
			, $fieldname
			, $def['label']
			, $def['name']
			, $def['type']
		);
	}
}
$d['content'] .= '</select><br />';

$data['content'] = CCTM::load_view('basic_form.php', $d);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/