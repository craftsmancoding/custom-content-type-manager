<?php
/*------------------------------------------------------------------------------
Controls the search form for the Post Content widget.
------------------------------------------------------------------------------*/
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
// See https://code.google.com/p/wordpress-summarize-posts/issues/detail?id=39
$post_type = CCTM::get_value($_POST, 'post_type', 'post');
$cap = 'edit_posts';
if (isset($GLOBALS['wp_post_types'][$post_type]->cap->edit_posts)) {
	$cap = $GLOBALS['wp_post_types'][$post_type]->cap->edit_posts; 
}
if (!current_user_can($cap)) die('<pre>You do not have permission to do that.</pre>');

require_once(CCTM_PATH.'/includes/CCTM_FormElement.php');
require_once(CCTM_PATH.'/includes/SummarizePosts.php');
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
require_once(CCTM_PATH.'/includes/GetPostsForm.php');


// Template Variables Initialization
$d = array(); 
$d['search_parameters'] = '';
$d['menu']				= '';
$d['fieldname']			= ''; // needed for thickbox.php
$d['fieldtype']         = ''; // needed for thickbox.php
$d['search_form']		= '';
$d['content']			= '';
$d['page_number']		= '0'; 
$d['orderby'] 			= 'ID';
$d['order'] 			= 'ASC';

///print '<pre>'.print_r($_POST, true).'</pre>';
//return;

// Generate a search form
// we do this AFTER the get_posts() function so the form can access the GetPostsQuery->args/defaults
$Form = new GetPostsForm();


//$d['content'] = '<pre>'.print_r($_POST, true) . '</pre>';

//! Validation
$args = array();
$post_id_field = CCTM::get_value($_POST, 'post_id_field');
$target_id = CCTM::get_value($_POST, 'target_id');
$args['post_type'] = CCTM::get_value($_POST, 'post_type');


// This gets subsequent search data that gets passed when the user refines the search.

if (isset($_POST['search_parameters'])) {


	//print '<pre> HERE...'. print_r($_POST['search_parameters'], true).'</pre>';
//	$d['content'] .= '<pre>HERE... '. print_r($_POST['search_parameters'], true).'</pre>';
	parse_str($_POST['search_parameters'], $args);

	// Pass the "view" parameters to the view
	$d['page_number'] = CCTM::get_value($args, 'page_number', 0);
	$d['orderby'] = CCTM::get_value($args, 'orderby', 'ID');
	$d['order'] = CCTM::get_value($args, 'order', 'ASC');
	
	// Unsest these, otherwise the query will try to search them as custom field values.
	unset($args['page_number']);
	unset($args['post_id_field']);
	unset($args['target_id']);	
}


// defaults....
$args['orderby'] = $d['orderby'];
$args['order'] = $d['order'];

// Set up search boundaries (i.e. the parameters used when nothing else is specified).
// Load up the config...
$possible_configs = array();
$possible_configs[] = '/config/post_selector/_post_content_widget.php'; 

CCTM::$post_selector = array();
if (!CCTM::load_file($possible_configs)) {
	print '<p>'.__('Post Selector configuration file not found.', CCTM_TXTDOMAIN) .'</p>';	
}


$search_parameters_str = ''; // <-- read custom search parameters, if defined.
if (isset($def['search_parameters'])) {
	$search_parameters_str = $def['search_parameters'];	
}
$additional_defaults = array();
parse_str($search_parameters_str, $additional_defaults);
//print '<pre>'.print_r($additional_defaults,true).'</pre>';
foreach($additional_defaults as $k => $v) {
	if (!empty($v)) {
		CCTM::$post_selector[$k] = $v;
	}
}


//------------------------------------------------------------------------------
// Begin!
//------------------------------------------------------------------------------
$Q = new GetPostsQuery(); 
// print '<pre>'.print_r(CCTM::$post_selector, true) . '</pre>';
//$Q->set_defaults(CCTM::$post_selector); // BRoken!

foreach(CCTM::$post_selector as $k => $v) {
	if (!isset($args[$k]) || empty($args[$k])) {
		$args[$k] = $v;
	}
}


$args['offset'] = 0; // assume 0, unless we got a page number
// Calculate offset based on page number
if (is_numeric($d['page_number']) && $d['page_number'] > 1) {
	$args['offset'] = ($d['page_number'] - 1) * CCTM::$post_selector['limit'];
}

// Get the results
$results = $Q->get_posts($args);


$search_form_tpl = CCTM::load_tpl(
	array('post_selector/search_forms/_post_content_widget.tpl')
);

$Form->set_tpl($search_form_tpl);
$Form->set_name_prefix(''); // blank out the prefixes
$Form->set_id_prefix('');
$search_by = array('search_term','yearmonth');

// Pass these to hidden fields so jQuery can read them
$Form->set_placeholder('post_type', $args['post_type']);
$Form->set_placeholder('post_id_field', $post_id_field);		
$Form->set_placeholder('target_id', $target_id);		

$d['search_form'] = $Form->generate($search_by, $args);


$item_tpl = '';
$wrapper_tpl = '';

$item_tpl = CCTM::load_tpl(
	array('post_selector/items/_widget.tpl')
);
$wrapper_tpl = CCTM::load_tpl(
	array('post_selector/wrappers/_default.tpl')
);



// Placeholders for the wrapper tpl
$hash = array();
$hash['post_title'] 	= __('Title', CCTM_TXTDOMAIN);
$hash['post_date'] 		= __('Date', CCTM_TXTDOMAIN);
$hash['post_status'] 	= __('Status', CCTM_TXTDOMAIN);
$hash['post_parent'] 	= __('Parent', CCTM_TXTDOMAIN);
$hash['post_type'] 		= __('Post Type', CCTM_TXTDOMAIN);
$hash['fieldname'] = ''; // needed for thickbox_inner.php
//$hash['filter'] 		= __('Filter', CCTM_TXTDOMAIN);
//$hash['show_all']		= __('Show All', CCTM_TXTDOMAIN);

$hash['content'] = '';

// And the items
//$results = array();
foreach ($results as $r){
	
	$r['preview'] = __('Preview', CCTM_TXTDOMAIN);
	$r['select'] = __('Select', CCTM_TXTDOMAIN);	
	$r['thumbnail_url'] = CCTM::get_thumbnail($r['ID']);	
	$hash['content'] .= CCTM::parse($item_tpl, $r);
}

// die(print_r($hash,true));
$d['content'] .= CCTM::parse($wrapper_tpl,$hash);

$d['content'] .= '<div class="cctm_pagination_links">'.$Q->get_pagination_links().'</div>';

if (isset($_POST['wrap_thickbox'])){
	print CCTM::load_view('templates/thickbox.php', $d);
}
else {
	//print CCTM::load_view('templates/thickbox_inner.php', $d);
//	print '<pre>'. $Q->debug() . '</pre>';
//	print '<pre>'. print_r($d, true) . '</pre>';
	print $d['content'];
}

exit;
/*EOF*/