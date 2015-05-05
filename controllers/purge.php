<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
* Purge unused post-types from the database
------------------------------------------------------------------------------*/


$data 				= array();
$data['page_title']	= __('Purge Posts', CCTM_TXTDOMAIN);
$data['help']		= 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Purge';
$data['menu'] 		= ''; 
$data['msg']		= CCTM::get_flash();
$data['action_name'] = 'custom_content_type_mgr_purge_posts';
$data['nonce_name'] = 'custom_content_type_mgr_purge_posts_nonce';
$data['submit']   = __('Delete', CCTM_TXTDOMAIN);
$data['fields']   = '';


// Show the post-types in the database.

global $wpdb;
$query = "SELECT post_type, count(*) as 'cnt' FROM {$wpdb->posts} WHERE post_type NOT IN ('revision','nav_menu_item') GROUP BY post_type";
$data['results'] = $wpdb->get_results( $query, OBJECT );

$pts = get_post_types();
// remove any post types you omitted in the query
unset($pts['revision']);
unset($pts['nav_menu_item']);

$data['active_cnt'] = count($pts);
$data['in_use_cnt'] = count($data['results']);

// you might have registered a post type that isn't actually used in the database
$data['inactive_cnt'] = $data['in_use_cnt'] - $data['active_cnt'];
if($data['inactive_cnt'] < 0) {
	$data['inactive_cnt'] = 0;  
}

// Assemble the inactive post-types
$inactive = array();
foreach ($data['results'] as &$r) {
	if ( !in_array($r->post_type, $pts) ) {
		$inactive[] = $r->post_type;
		$r->post_type = $r->post_type . ' (disabled)';
	}
}



// If properly submitted, Proceed with purging the database
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {

	if (!$data['inactive_cnt']) {
		// Nothing to do
		$msg = CCTM::format_msg(__('Your database is clean: there are no inactive post-types that need to be purged.', CCTM_TXTDOMAIN));
		self::set_flash($msg);
		include( CCTM_PATH . '/controllers/tools.php');	
		return;
	}
	
	// Purge the database
	$post_type_str = '';
	foreach ($inactive as &$in) {
		$in = $wpdb->prepare('%s', $in);
	}
	$post_type_str = implode(',', $inactive);

	global $wpdb;
	
	// Delete the custom fields
	$query = "DELETE FROM {$wpdb->postmeta} WHERE post_id IN ( SELECT ID FROM {$wpdb->posts} WHERE post_type IN ($post_type_str) )";
	$wpdb->query($query);
	
	// Delete taxonomy refs
	$query = "DELETE FROM {$wpdb->term_relationships} WHERE object_id IN ( SELECT ID FROM {$wpdb->posts} WHERE post_type IN ($post_type_str) )";
	$wpdb->query($query);
		
	// Delete any revisions, e.g.
	// DELETE a FROM wp_posts a INNER JOIN wp_posts b ON a.post_parent=b.ID WHERE a.post_type='revision' AND b.post_type='post'
	$query = $wpdb->prepare("DELETE a FROM {$wpdb->posts} a INNER JOIN {$wpdb->posts} b ON a.post_parent=b.ID WHERE a.post_type='revision' AND b.post_type IN ($post_type_str)"
		, $post_type);
	$wpdb->query($query);
			
	// Delete the posts
	$query = $wpdb->prepare("DELETE FROM {$wpdb->posts} WHERE post_type IN ($post_type_str);"
		, $post_type);
	$wpdb->query($query);		

	
	
	$msg = '<div class="updated"><p>'
		.sprintf( __('The database has been purged from the following post-types: %s', CCTM_TXTDOMAIN), "<em>$post_type_str</em>")
		. '</p></div>';
	self::set_flash($msg);
	
	// Redirect
	include( CCTM_PATH . '/controllers/tools.php');
	
	return;
}




if (!$data['inactive_cnt']) {
	$data['content'] = sprintf('<h4>%s</h4>', __('Your database is clean: there are no inactive post-types that need to be purged.', CCTM_TXTDOMAIN));
}
else {
	$data['content'] = sprintf('<h4>%s</h4>', __('The following post-types are inactive and can be purged from your database:', CCTM_TXTDOMAIN));
	$data['content'] .= '<ul>';
	foreach ($inactive as $pt) {
		$data['content'] .= sprintf('<li>%s</li>', $pt);
	}
	$data['content'] .= '</ul>';
	$data['content'] .= '<div class="error">';
	$data['content'] .= '<p>'.__('Deleting these from your database cannot be undone!', CCTM_TXTDOMAIN).'</p>';
	$data['content'] .= '</div>';
}

$data['content'] = CCTM::load_view('purge.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/