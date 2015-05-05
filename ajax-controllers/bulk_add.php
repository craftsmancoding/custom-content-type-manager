<?php
/*------------------------------------------------------------------------------
This adds a custom field row to the "Bulk Add" table. This handles 2 types of 
posting actions:
1. User selects a field type
2. User wants to automatically add fields based on a post_type and optional post-ids

Remember: the output here MUST be wrapped in HTML tags, otherwise jQuery's .html()
method will kack.
------------------------------------------------------------------------------*/
if (!defined('CCTM_PATH')) exit('No direct script access allowed');

$desired_field_type = CCTM::get_value($_POST, 'field_type'); 
$post_type = CCTM::get_value($_POST, 'post_type');
$post_ids = CCTM::get_value($_POST, 'post_ids');

// Stuff we're using in our view...
$data = array();
$data['i'] = md5(uniqid()); // we just need a unique identifier for each row element
$data['name'] = '';
$data['label'] = '';
$data['type'] = '';
$data['field_types'] = '';
$data['name_class'] = ''; // 'cctm_validation_error';


// 1. User has specified a desired field type
if ($desired_field_type) {
    $elements = CCTM::get_available_helper_classes('fields');
    foreach ( $elements as $field_type => $file ) {
    	if ($FieldObj = CCTM::load_object($field_type,'fields') ) {
    	   $is_selected = '';
    	   if ($desired_field_type == $field_type) {
        	   $is_selected = ' selected="selected"';    
    	   }
            $data['field_types'] .= '<option value="'.$field_type.'"'.$is_selected.'>'.$FieldObj->get_name().'</option>';		
    	}
    	else {
            // Form element not found.  Did someone move a custom class file?
    	}
    }
    
    print CCTM::load_view('tr_bulk.php', $data);
}
// 2. User wants to query the database
elseif ($post_type || $post_ids) {
    // Search the database 
}
else {
	printf('<div class="error"><table><tr><td><img src="%s/images/warning-icon.png" height="44" width="50"/></td><td><p>%s</p></td></tr></table></div>', CCTM_URL, __('Invalid selection.', CCTM_TXTDOMAIN));
	return;
}


/*EOF*/