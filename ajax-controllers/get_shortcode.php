<?php
//------------------------------------------------------------------------------
/**
 * Gets the shortcode for the TinyMCE editor.  When the summarize_posts_form.php 
 * form is posted, it does an AJAX submit to this controller, with the form
 * data serialized inside of $_POST['search_parameters'] 
 * it arrives something like this:
 * post_type%5B%5D=attachment&taxonomy=&taxonomy_term=&post_parent=&meta_key=model&meta_value=Ford
 *
 */
require_once(CCTM_PATH.'/includes/SummarizePosts.php');
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');

$args = array(); // initialize

parse_str($_POST['search_parameters'], $args);
//print '<pre>'.print_r($args,true) . '</pre>';
$Q = new GetPostsQuery($args);
//print $Q->debug();
print $Q->get_shortcode();
/*EOF*/