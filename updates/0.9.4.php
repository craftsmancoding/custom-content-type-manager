<?php if ( ! defined('CCTM_UPDATE_MODE')) exit('Run in update mode only.');
/*------------------------------------------------------------------------------
This is run when the user updates to version 0.9.4 of the plugin. It is executed
via a simple include from within the CCTM class.

It is important that this update refer to the legacy name of the option:
'custom_content_types_mgr_data'

Summary of changes:

1. Upate Icon Path

The path used for custom post-type icons was changed from images/icons/default
to images/icons, but any post-types already using a custom icon will maintain 
the old path in the data structure, so this grooms through the data structure
and updates the paths to the new location to avoid broken icon paths in the 
manager.

2. Migrate Data Structure

See http://code.google.com/p/wordpress-custom-content-type-manager/wiki/DataStructures

See this issue:
http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=219
*some* fields use the integer array key, some have the string key.  Yarrgh.

The biggest problem here is that custom field definitions were not normalized, so
the easy way out is to define a new field for any name conflicts, e.g. "my_dropdown"
in the "books" post-type is assumed to be UNEQUAL to the "my_dropdown" defined for 
the "movies" post-type. 

------------------------------------------------------------------------------*/
function _____sort_custom_fields($field, $sortfunc) {
	return create_function('$var1, $var2', 'return '.$sortfunc.'($var1["'.$field.'"], $var2["'.$field.'"]);');
}

$data = get_option('custom_content_types_mgr_data', array());


//! 1. Update Icon Path
foreach ($data as $post_type => &$def) {
	if (isset($def['use_default_menu_icon']) && empty($def['use_default_menu_icon']) && isset($def['menu_icon']) ) {
		$def['menu_icon'] = preg_replace('#default/#', '', $def['menu_icon']);
	}
}


//! 2. Migrate Data Structure
$new_data = array();
// And pop in some of the anticipated new nodes in the structure
$new_data['flash'] = array();
$new_data['locks'] = array();
$new_data['warnings'] = array();
$new_data['post_type_defs'] = array();
$new_data['custom_field_defs'] = array();
$new_data['cctm_installation_timestamp'] = time(); // it's not REAL, but it's close
$new_data['export_info'] = array(
	'title' 		=> 'CCTM Site',
	'author' 		=> get_option('admin_email',''),
	'url' 			=> get_option('siteurl','http://wpcctm.com/'),
	'template_url'	=> '',
	'description'	=> __('This site was created in part using the Custom Content Type Manager', CCTM_TXTDOMAIN),
);
// grab the other option...
$settings = get_option('custom_content_types_mgr_settings', array() );
if ( isset($settings['export_info']) ) {
	$new_data['export_info'] = $settings['export_info'];
}

// get custom fields from old 
foreach ($data as $post_type => &$def) {

	if (isset($def['public']) && $def['public']) {
		$def['publicly_queriable'] = true;
		$def['show_ui'] = true;
		$def['show_in_nav_menus']  = true;
		$def['exclude_from_search'] = false;
	}
	unset($def['custom_content_type_mgr_create_new_content_type_nonce']);
	
	$custom_fields_this_post_type = array();
	
	if ( isset($def['custom_fields']) && is_array($def['custom_fields'])) {
		// usort pushes everything back to an integer key
		// This step also consolidates any instances where a custom field def has both an
		// integer key AND a string key (yes, this happened somehow).
		@usort($def['custom_fields'], _____sort_custom_fields('sort_param', 'strnatcasecmp'));
		foreach ($def['custom_fields'] as $i => $field_def) {
			$fieldname = $field_def['name'];
			$def['custom_fields'][$fieldname] = $field_def;
			unset($def['custom_fields'][$i]); // clean out integer keys
		}
 
		foreach ($def['custom_fields'] as $fieldname => $field_def) { 
			if (is_numeric($fieldname)) {
				continue; // <-- skip any rogue integer keys
			}
			unset($field_def['sort_param']);  // we don't use this in 0.9.4			
			$original_fieldname = $fieldname;
			
			// being lazy... assuming there aren't more than 10 fields with the same name
			if ( isset($new_data['custom_field_defs'][$fieldname]) ) {
				$fieldname++;
			}
			if ( isset($new_data['custom_field_defs'][$fieldname]) ) {
				$fieldname++;
			}
			if ( isset($new_data['custom_field_defs'][$fieldname]) ) {
				$fieldname++;
			}
			if ( isset($new_data['custom_field_defs'][$fieldname]) ) {
				$fieldname++;
			}
			if ( isset($new_data['custom_field_defs'][$fieldname]) ) {
				$fieldname++;
			}
			if ( isset($new_data['custom_field_defs'][$fieldname]) ) {
				$fieldname++;
			}
			if ( isset($new_data['custom_field_defs'][$fieldname]) ) {
				$fieldname++;
			}
			if ( isset($new_data['custom_field_defs'][$fieldname]) ) {
				$fieldname++;
			}
			if ( isset($new_data['custom_field_defs'][$fieldname]) ) {
				$fieldname++;
			}						
			if ( isset($new_data['custom_field_defs'][$fieldname]) ) {
				$fieldname++;
			}
			
			$custom_fields_this_post_type[] = $fieldname;
			$field_def['name'] = $fieldname;
			$new_data['custom_field_defs'][$fieldname] = $field_def;	
		}
		
		$data[$post_type]['custom_fields'] = $custom_fields_this_post_type;

		// Alert users to the fact that they may have to change their templates!!!
		if ($fieldname != $original_fieldname) {
			$msg = sprintf( __("You may have to change your template for the %s post_type! Any instances of get_custom_field('%s') or print_custom_field('%s') in the single-%s.php file should be replaced with get_custom_field('%s') or print_custom_field('%s').  You may also use the 'Custom Fields-->Merge' command to merge field definitions.", CCTM_TXTDOMAIN) 
				, $post_type
				, $original_fieldname
				, $original_fieldname
				, $post_type
				, $fieldname
				, $fieldname
			);
			CCTM::register_warning($msg);
			// update database
			global $wpdb;
			$wpdb->prepare( "UPDATE 
                    $wpdb->postmeta postmeta INNER JOIN $wpdb->posts posts
                    SET postmeta.meta_key = %s
                    WHERE
                    posts.post_type = %s 
                    AND
                    postmeta.meta_key = %s"
                    
	            , $fieldname
    	        , $post_type
    	        , $original_fieldname
            );
		}
	}	
} // post_type loop


$new_data['post_type_defs'] = $data;


//print_r($new_data); exit;
update_option( self::db_key, $new_data ); // stick it in the db
self::$data = $new_data; // and stick it in memory just to be sure
delete_option('custom_content_types_mgr_data'); // legacy pre 0.9.4
delete_option('custom_content_types_mgr_settings'); // legacy pre 0.9.4

/*EOF*/