<?php
/*------------------------------------------------------------------------------
This controller displays a selection of posts in a lightbox/thickbox for the 
user to select when they arecreating or editing a post/page, 
i.e. the "Post Selector"

The thickbox appears (for example) when you create or edit a post that uses a 
relation, image, or media field.
------------------------------------------------------------------------------*/
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
// See https://code.google.com/p/wordpress-summarize-posts/issues/detail?id=39
$post_type = CCTM::get_value($_POST, 'post_type', 'post');
$cap = 'edit_posts';
if (isset($GLOBALS['wp_post_types'][$post_type]->cap->edit_posts)) {
	$cap = $GLOBALS['wp_post_types'][$post_type]->cap->edit_posts; 
}
if (!current_user_can($cap)) die('<pre>You do not have permission to do that.</pre>');
require_once CCTM_PATH.'/includes/CCTM_FormElement.php';
require_once CCTM_PATH.'/includes/SummarizePosts.php';
require_once CCTM_PATH.'/includes/GetPostsQuery.php';
require_once CCTM_PATH.'/includes/GetPostsForm.php';

//print '<pre>'.print_r($_POST,true).'</pre>'; exit;
// Template Variables Initialization
$d = array(); 
$d['search_parameters'] = '';
$d['fieldname'] 		= '';
$d['fieldtype']         = '';
$d['menu']				= '';
$d['search_form']		= '';
$d['content']			= '';
$d['page_number']		= '0'; 
$d['orderby'] 			= 'ID';
$d['order'] 			= 'ASC';
$d['exclude'] = CCTM::get_value($_POST, 'exclude');


// Generate a search form
// we do this AFTER the get_posts() function so the form can access the GetPostsQuery->args/defaults
$Form = new GetPostsForm();

//! Validation
// Some Tests first to see if the request is valid...
$raw_fieldname = CCTM::get_value($_POST, 'fieldname');
$d['fieldtype'] = CCTM::get_value($_POST, 'fieldtype');

if (empty($raw_fieldname) && empty($d['fieldtype'])) {
	print '<pre>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($raw_fieldname).'</em>') .'</pre>';
	return;
}
// More Template Variables
$d['fieldname'] = $raw_fieldname;

$fieldname = preg_replace('/^'. CCTM_FormElement::css_id_prefix . '/', '', $raw_fieldname);

$def = CCTM::get_value(CCTM::$data['custom_field_defs'], $fieldname);
//print '<pre>'.print_r($def, true).'</pre>';
if (!empty($d['fieldtype'])) {
	$def['type'] = $d['fieldtype'];
}
elseif (empty($def)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($fieldname).'</em>').'</p>';
	return;
}

// Set up search boundaries (i.e. the parameters used when nothing else is specified).
// Load up the config...
$possible_configs = array();
$possible_configs[] = '/config/post_selector/'.$fieldname.'.php'; 	// e.g. my_field.php
$possible_configs[] = '/config/post_selector/_'.$def['type'].'.php'; 		// e.g. _image.php
$possible_configs[] = '/config/post_selector/_relation.php'; 		// default
//print '<pre>'.print_r($possible_configs,true).'</pre>'; exit;
CCTM::$post_selector = array();
CCTM::$search_by = true; // all options available if the tpl passes them
if (!CCTM::load_file($possible_configs)) {
	print '<p>'.__('Post Selector configuration file not found.', CCTM_TXTDOMAIN) .'</p>';	
}


// This gets subsequent search data that gets passed when the user refines the search.
$args = array();
// Do not set defaults here! It causes any values set in the config/post_selector/ files
// to be ignored. See https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=537
//$args['orderby'] = 'ID';
//$args['order'] = 'ASC';

if (isset($_POST['search_parameters'])) {
    // e.g. fieldname=movie_clip&fieldtype=media&page_number=0&orderby=ID&order=ASC
	parse_str($_POST['search_parameters'], $args);

    //print '<pre>'.print_r($args,true).'</pre>'; exit;
    
	// Pass the "view" parameters to the view
	$d['page_number'] = CCTM::get_value($args, 'page_number', 0);
	$d['orderby'] = CCTM::get_value($args, 'orderby', 'ID');
	$d['order'] = CCTM::get_value($args, 'order', 'ASC');
	
	// Unsest these, otherwise the query will try to search them as custom field values.
	unset($args['page_number']);
	unset($args['fieldname']);
	unset($args['fieldtype']);
}

// Set search boundaries (i.e. the parameters used when nothing is specified)
// !TODO: put this configuration stuff into the /config/ files

// optionally get pages to exclude
if (isset($_POST['exclude'])) {
	CCTM::$post_selector['exclude'] = $_POST['exclude'];
}

$search_parameters_str = ''; // <-- read custom search parameters, if defined.
if (isset($def['search_parameters'])) {
	$search_parameters_str = $def['search_parameters'];	
}
$additional_defaults = array();
parse_str($search_parameters_str, $additional_defaults);

foreach($additional_defaults as $k => $v) {
	if (!empty($v)) {
		CCTM::$post_selector[$k] = $v;
	}
}
//print '<pre>'.print_r(CCTM::$post_selector,true).'</pre>'; exit;

//------------------------------------------------------------------------------
// Begin!
//------------------------------------------------------------------------------
$Q = new GetPostsQuery(); 

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
//print '<pre>'.print_r($args,true).'</pre>'; exit;

// Set pagination tpls
$tpls = array (
	'firstTpl'		=> '<span class="linklike" onclick="javascript:change_page(1);">&laquo; First</span> &nbsp;',
	'lastTpl' 		=> '&nbsp;<span class="linklike" onclick="javascript:change_page([+page_number+]);" >Last &raquo;</span>',
	'prevTpl' 		=> '<span class="linklike" onclick="javascript:change_page([+page_number+]);">&lsaquo; Prev.</span>&nbsp;',
	'nextTpl' 		=> '&nbsp;<span class="linklike" onclick="javascript:change_page([+page_number+]);">Next &rsaquo;</span>',
	'currentPageTpl'=> '&nbsp;<span class="post_selector_pagination_active_page">[+page_number+]</span>&nbsp;',
	'pageTpl' 		=> '&nbsp;<span class="linklike" title="[+page_number+]" onclick="javascript:change_page([+page_number+]);">[+page_number+]</span>&nbsp;',
	'outerTpl' 		=> '<div id="pagination">[+content+] &nbsp; &nbsp;
		Page [+current_page+] of [+page_count+]<br/>
	</div>',
);
$Q->set_tpls($tpls);

// Get the results
//print '<pre>'.print_r(CCTM::$search_by, true) . '</pre>';
$results = $Q->get_posts($args);
//print '<pre>'.$Q->debug().'</pre>';
$search_form_tpl = CCTM::load_tpl(
	array('post_selector/search_forms/'.$fieldname.'.tpl'
		, 'post_selector/search_forms/_'.$def['type'].'.tpl'
		, 'post_selector/search_forms/_default.tpl'
	)
);

$Form->set_tpl($search_form_tpl);
$Form->set_name_prefix(''); // blank out the prefixes
$Form->set_id_prefix('');

$d['search_form'] = $Form->generate(CCTM::$search_by, $args);

$item_tpl = '';
$wrapper_tpl = '';

// Multi Field (contains an array of values.
if (isset($def['is_repeatable']) && $def['is_repeatable'] == 1) {

	$item_tpl = CCTM::load_tpl(
		array('post_selector/items/'.$fieldname.'.tpl'
			, 'post_selector/items/_'.$def['type'].'_multi.tpl'
			, 'post_selector/items/_relation_multi.tpl'
		)
	);
	$wrapper_tpl = CCTM::load_tpl(
		array('post_selector/wrappers/'.$fieldname.'.tpl'
			, 'post_selector/wrappers/_'.$def['type'].'_multi.tpl'
			, 'post_selector/wrappers/_relation_multi.tpl'
		)
	);
}
// Simple field (contains single value)
else {	
	$item_tpl = CCTM::load_tpl(
		array('post_selector/items/'.$fieldname.'.tpl'
			, 'post_selector/items/_'.$def['type'].'.tpl'
			, 'post_selector/items/_default.tpl'
		)
	);
	$wrapper_tpl = CCTM::load_tpl(
		array('post_selector/wrappers/'.$fieldname.'.tpl'
			, 'post_selector/wrappers/_'.$def['type'].'.tpl'
			, 'post_selector/wrappers/_default.tpl'
		)
	);
}


// Placeholders for the wrapper tpl
$hash = array();
$hash['post_title'] 	= __('Title', CCTM_TXTDOMAIN);
$hash['post_date'] 		= __('Date', CCTM_TXTDOMAIN);
$hash['post_status'] 	= __('Status', CCTM_TXTDOMAIN);
$hash['post_parent'] 	= __('Parent', CCTM_TXTDOMAIN);
$hash['post_type'] 		= __('Post Type', CCTM_TXTDOMAIN);
$hash['add_to_post'] 	= __('Add to Post', CCTM_TXTDOMAIN);
$hash['add_to_post_and_close'] = __('Add to Post and Close', CCTM_TXTDOMAIN);

//$hash['filter'] 		= __('Filter', CCTM_TXTDOMAIN);
//$hash['show_all']		= __('Show All', CCTM_TXTDOMAIN);

$hash['content'] = '';

// And the items
//$results = array();
foreach ($results as $r){
	
	$r['name'] = $raw_fieldname;
	$r['preview'] = __('Preview', CCTM_TXTDOMAIN);
	$r['select'] = __('Select', CCTM_TXTDOMAIN);	
	$r['field_id'] = $raw_fieldname;
	$r['thumbnail_url'] = CCTM::get_thumbnail($r['ID']);
	// Translate stuff (issue 279)
	$r['post_title'] = __($r['post_title']);
	$r['post_content'] = __($r['post_content']);
	$r['post_excerpt'] = __($r['post_excerpt']);
	
	$hash['content'] .= CCTM::parse($item_tpl, $r);
}

$d['content'] .= CCTM::parse($wrapper_tpl,$hash);

$d['content'] .= '<div class="cctm_pagination_links">'.$Q->get_pagination_links().'</div>';

if (isset($_POST['wrap_thickbox'])){
	print CCTM::load_view('templates/thickbox.php', $d);
}
else {
	//print CCTM::load_view('templates/thickbox_inner.php', $d);
	print $d['content'];
}

exit;
/*EOF*/