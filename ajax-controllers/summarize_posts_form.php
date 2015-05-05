<?php
/*------------------------------------------------------------------------------
This powers the TinyMCE thumbtack button that displays a SummarizePosts search
form.

Remember: the output here MUST be wrapped in HTML tags, otherwise jQuery's .html()
method will kack.
------------------------------------------------------------------------------*/
require_once(CCTM_PATH.'/includes/SummarizePosts.php');
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
require_once(CCTM_PATH.'/includes/GetPostsForm.php');

$Form = new GetPostsForm();

// What options should be displayed on the form that defines the search?  
// Load up the config...
$possible_configs = array();
$possible_configs[] = '/config/search_parameters/_summarize_posts.php';

if (!CCTM::load_file($possible_configs)) {
	print '<p>'.__('Search parameter configuration file not found.', CCTM_TXTDOMAIN) .'</p>';	
}

$form_tpl = CCTM::load_tpl('summarize_posts/search.tpl');

$Form->set_name_prefix('');
$Form->set_id_prefix('');
$Form->set_placeholder('cctm_url', CCTM_URL);
$Form->set_tpl($form_tpl);

$custom_fields = CCTM::get_custom_field_defs();
$custom_field_options = '';
foreach($custom_fields as $cf) {
	$custom_field_options .= sprintf('<option value="%s:%s">%s</option>', $cf['name'], $cf['label'], $cf['label']);
}
$Form->set_placeholder('custom_fields', $custom_field_options);

// I18n for the search form
$Form->set_placeholder('widget_desc', __('Dynamically list posts according to the criteria below.', CCTM_TXTDOMAIN));
$Form->set_placeholder('post_title_label', __('Post Title', CCTM_TXTDOMAIN));
$Form->set_placeholder('author_id_label', __('Author ID', CCTM_TXTDOMAIN));
$Form->set_placeholder('add_filter_label', __('Add Filter', CCTM_TXTDOMAIN));
$Form->set_placeholder('generate_shortcode_label', __('Generate Shortcode', CCTM_TXTDOMAIN));
$Form->set_placeholder('cancel_label', __('Cancel', CCTM_TXTDOMAIN));

print $Form->generate(CCTM::$search_by);



//print '<pre>hey...</pre>';
/*EOF*/