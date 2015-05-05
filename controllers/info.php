<?php 
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Information about this plugin
// Too late!
//wp_enqueue_style( 'google-charts', 'https://www.google.com/jsapi');
// wp_enqueue_script( 'google-charts');

http://code.google.com/apis/chart/interactive/docs/gallery/piechart.html
------------------------------------------------------------------------------*/
$data=array();
$data['page_title'] = __('Information', CCTM_TXTDOMAIN);
$data['msg'] = '';
$data['menu'] = '';

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

foreach ($data['results'] as &$r) {
	if ( !in_array($r->post_type, $pts) ) {
		$r->post_type = $r->post_type . ' (disabled)';
	}
}

$data['content'] = CCTM::load_view('info.php', $data);
print CCTM::load_view('templates/default.php', $data);
/*EOF*/

