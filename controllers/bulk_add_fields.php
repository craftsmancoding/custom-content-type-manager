<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
require_once CCTM_PATH.'/includes/GetPostsQuery.php';
//------------------------------------------------------------------------------
/**
 * Show all available types of Custom Fields for bulk adding.
 *
 */
$default_field_type='text';
$data=array();
$data['page_title'] = __('Add Multiple Fields', CCTM_TXTDOMAIN);
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/SupportedCustomFields';
$data['msg'] = self::get_flash();
$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_fields&a=list_custom_fields" class="button">%s</a>', __('Back', CCTM_TXTDOMAIN) );
$data['action_name']  = 'custom_content_type_mgr_bulk_add_fields';
$data['nonce_name']  = 'custom_content_type_mgr_bulk_add_fields_nonce';
$data['fields'] = '';
$data['field_types'] = '';
$data['content'] = '';
$data['style'] = file_get_contents(CCTM_PATH.'/css/validation.css');

global $wpdb;

// Get all avail. field types: used for icons and for dropdowns.
$elements = CCTM::get_available_helper_classes('fields');
foreach ( $elements as $field_type => $file ) {
	if ($FieldObj = CCTM::load_object($field_type,'fields') ) {
		$d = array();		
		$d['name'] 			= $FieldObj->get_name();
		$d['icon'] 			= $FieldObj->get_icon();
		$d['type'] 			= $field_type;
		// The option is used for dropdowns
        $is_selected = '';
        if ($default_field_type == $field_type) {
           $is_selected = ' selected="selected"';    
        }		
        $data['field_types'] .= '<option value="'.$field_type.'"'.$is_selected.'>'.$FieldObj->get_name().'</option>';				
		$data['fields'] .= CCTM::load_view('bulk_icon.php',$d);	
	}
	else {
		$data['field_types'] .= sprintf(
			__('Form element not found: %s', CCTM_TXTDOMAIN)
			, "<code>$field_type</code>"
		);
	}
}

// Load up all defined fields: we need to know what has already been defined.
$defs = CCTM::get_custom_field_defs();

// We don't care about what post-types have been registered, only what exists in the db
$query = "SELECT DISTINCT post_type FROM {$wpdb->posts} WHERE post_type NOT IN ('revision','nav_menu_item')";
$data['results'] = $wpdb->get_results( $query, OBJECT );

if (!empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
    // Is this a save request?  Or a search request?
    //    print '<pre>'.print_r($_POST,true).'</pre>'; exit;
    // Search
    if (isset($_POST['post_type_pad']) && isset($_POST['post_ids'])) {
        $post_ids = array();
        if (!empty($_POST['post_ids'])) {
            $post_ids = explode(',',$_POST['post_ids']);
        }
        if (!empty($_POST['post_type_pad'])) {
            $Q = new GetPostsQuery();
            $args = array();
            $args['limit'] = 10;
            if ($_POST['post_type_pad']) {
                $args['post_type'] = $_POST['post_type_pad'];
            }
            if ($_POST['post_ids']) {
                $args['ID']['in'] = $_POST['post_ids'];
            }
            $results = $Q->get_posts($args);
            foreach ($results as $r) {
                $post_ids[] = $r['ID'];
            }
        }
        
        foreach ($post_ids as &$pid) {
            $pid = (int) $pid;
        }

        $query = "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} 
        WHERE post_id IN (".implode(',',$post_ids).")
        AND meta_key NOT LIKE '\_%'";
//        print '<pre>'.$query.'</pre>'; exit;
        $results = $wpdb->get_results( $query, ARRAY_A );
        foreach ($results as $r) {
            $tpl = 'tr_bulk.php';
            if (isset($defs[ $r['meta_key'] ])) $tpl='tr_bulk_disabled.php'; // skip fields already defined.
            $d = array();
            $d['i'] = md5(uniqid()); // we just need a unique identifier for each row element
            $d['name'] = htmlentities($r['meta_key']);
            $d['label'] = htmlentities(ucfirst($r['meta_key']));
            $d['type'] = 'text';
            $d['field_types'] = $data['field_types'];
            $d['name_class'] = '';
            $data['fields'] .= CCTM::load_view($tpl, $d);        
        }

    }
    // Save
    elseif (isset($_POST['fields'])) {
        // 1st pass: check for errors
        $errors = false;
        foreach ($_POST['fields'] as $tmp => &$def) {
            if (empty($def['name'])) {
                $errors[ $def['name'] ] = true;
                $data['msg'] = '<div class="error"><p>'.__('Each field must have a name.', CCTM_TXTDOMAIN)
					.'</p></div>';
            }
            elseif (!preg_match('/^[a-z]{1}[a-z_0-9]*$/i', $def['name'])) {
				$errors[ $def['name'] ] = true;
                $data['msg'] = '<div class="error"><p>'.__('One or more of your fields contains invalid characters in its name. The name may only contain letters, numbers, and underscores, and it must begin with a letter. Invalid characters have been filtered for you.', CCTM_TXTDOMAIN)
					.'</p></div>';
				$def['name'] = preg_replace('/[^a-z_0-9]/', '', $def['name']);
			}
        }
        
        // Re-display it
        if (!empty($errors)) {
            foreach ($_POST['fields'] as $tmp => $def) {
                $d = array();
                $d['i'] = md5(uniqid()); // we just need a unique identifier for each row element
                $d['name'] = htmlentities($def['name']);
                $d['label'] = htmlentities($def['label']);
                $d['type'] = '';
                $d['field_types'] = $data['field_types'];
                $d['name_class'] = '';
                if (isset($errors[ $def['name'] ])) {
                    $d['name_class'] = 'cctm_validation_error';
                }
                $data['fields'] .= CCTM::load_view('tr_bulk.php', $d);
            }
        }
        // Save it
        else {
            //print '<pre>'.print_r($_POST['fields'],true).'</pre>'; exit;
            foreach ($_POST['fields'] as $tmp => $def) {
                $field_name = $def['name'];
                if (!isset(self::$data['custom_field_defs'][$field_name])) {
                    self::$data['custom_field_defs'][$field_name] = $def;                
                }
            }
            
            $success_msg = sprintf('<div class="updated"><p>%s</p></div>'
                , __('Your custom fields have been created.', CCTM_TXTDOMAIN));
    		update_option( self::db_key, self::$data );
    		unset($_POST);
    		self::set_flash($success_msg);
            include CCTM_PATH.'/controllers/list_custom_fields.php';
            return;
        }
    }
    else {
        $data['msg'] = '<div class="error"><p>'.__('Please add fields before saving.',CCTM_TXTDOMAIN).'</p></div>';                        
    }

}

$data['content'] .= CCTM::load_view('bulk_fields.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/