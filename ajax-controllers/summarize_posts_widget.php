<?php
/*------------------------------------------------------------------------------
This is what drives the search forms that pop when you define a Summarize Posts 
Widget: Appearance --> Widgets --> (add a Summarize Posts Widget to a widget area)
--> Click the "Define Search" button

Remember: the output here MUST be wrapped in HTML tags, otherwise jQuery's .html()
method will kack.
------------------------------------------------------------------------------*/
require_once(CCTM_PATH.'/includes/SummarizePosts.php');
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
require_once(CCTM_PATH.'/includes/GetPostsForm.php');

// Id of the field that will store the values (we need to pass it through here
// because it is not static: there may be multiple instances of the widget)
$storage_field = CCTM::get_value($_POST,'storage_field', 'storage_field');

$search_parameters_str = '';
if (isset($_POST['search_parameters'])) {
	$search_parameters_str = $_POST['search_parameters'];
}

$existing_values = array();
parse_str($search_parameters_str, $existing_values);


$Form = new GetPostsForm();

// What options should be displayed on the form that defines the search?  
// Load up the config...
$possible_configs = array();
$possible_configs[] = '/config/search_parameters/_widget.php';

if (!CCTM::load_file($possible_configs)) {
	print '<p>'.__('Search parameter configuration file not found.', CCTM_TXTDOMAIN) .'</p>';	
}

$form_tpl = CCTM::load_tpl('summarize_posts/widget.tpl');

$Form->set_name_prefix('');
$Form->set_id_prefix('');

$Form->set_tpl($form_tpl);

$custom_fields = CCTM::get_custom_field_defs();
$custom_field_options = '';
foreach($custom_fields as $cf) {
	$custom_field_options .= sprintf('<option value="%s:%s">%s</option>', $cf['name'], $cf['label'], $cf['label']);
}

if (!isset($existing_values['limit']) || $existing_values['limit'] == 0) {
	$existing_values['limit'] = 5;
}

$Form->set_placeholder('custom_fields', $custom_field_options);
$Form->set_placeholder('cctm_url', CCTM_URL);
$Form->set_placeholder('storage_field', $storage_field);

// I18n for the widget
$Form->set_placeholder('widget_desc', __('Dynamically list posts according to the criteria below.', CCTM_TXTDOMAIN));
$Form->set_placeholder('post_title_label', __('Post Title', CCTM_TXTDOMAIN));
$Form->set_placeholder('author_id_label', __('Author ID', CCTM_TXTDOMAIN));
$Form->set_placeholder('add_filter_label', __('Add Filter', CCTM_TXTDOMAIN));
$Form->set_placeholder('save_criteria_label', __('Save Criteria', CCTM_TXTDOMAIN));
$Form->set_placeholder('cancel_label', __('Cancel', CCTM_TXTDOMAIN));

print $Form->generate(CCTM::$search_by, $existing_values);


exit;
/*EOF*/