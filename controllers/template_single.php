<?php
//------------------------------------------------------------------------------
/**
 * Manager Page -- called by page_main_controller()
 * Show what a single page for this custom post-type might look like.  This is
 * me throwing a bone to template editors and creators.
 *
 * I'm using a tpl and my parse() function because I have to print out sample PHP
 * code... otherwise it's too much of a pain to include PHP without it executing.
 *
 * @param string  $post_type
 * @package
 */
 
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
require_once(CCTM_PATH.'/includes/CCTM_PostTypeDef.php');
require_once(CCTM_PATH.'/includes/CCTM_OutputFilter.php');


$data     = array();
$data['page_title'] = sprintf(__('Sample Themes for %s', CCTM_TXTDOMAIN), "<em>$post_type</em>");
$data['help']  = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/SampleTemplates';
$data['menu']   = sprintf('<a href="'.get_admin_url(false, 'admin.php').'?page=cctm&a=list_post_types" class="button">%s</a>', __('Back', CCTM_TXTDOMAIN) )
	. ' ' . sprintf('<a href="'.get_admin_url(false, 'admin.php').'?page=cctm&a=list_custom_field_types&pt=%s" class="button">%s</a>', $post_type, __('Create Custom Field for this Post Type', CCTM_TXTDOMAIN) );
$data['msg']  = '';
$data['post_type'] = $post_type;

// Validate post type
if (!CCTM_PostTypeDef::is_existing_post_type($post_type) ) {
	include(CCTM_PATH.'/controllers/error.php');
	return;
}

$current_theme_name = wp_get_theme();
$current_theme_path = get_stylesheet_directory();

$hash = array();

$tpl = file_get_contents( CCTM_PATH.'/tpls/samples/single_post.tpl');
$tpl = htmlspecialchars($tpl);

$data['single_page_msg'] = sprintf( __('WordPress supports a custom theme file for each registered post-type (content-type). Copy the text below into a file named <strong>%s</strong> and save it into your active theme.', CCTM_TXTDOMAIN)
	, 'single-'.$post_type.'.php'
);
$data['single_page_msg'] .= sprintf( __('You are currently using the %1$s theme. Save the file into the %2$s directory.', CCTM_TXTDOMAIN)
	, '<strong>'.$current_theme_name.'</strong>'
	, '<strong>'.$current_theme_path.'</strong>'
);


// built-in content types don't verbosely display what fields they display
/* Array
(
[product] => Array
(
    [supports] => Array
        (
            [0] => title
            [1] => editor
            [2] => author
            [3] => thumbnail
            [4] => excerpt
            [5] => trackbacks
            [6] => custom-fields
        )
*/

// Check the TYPE of custom field to handle image and relation custom fields.
// title, author, thumbnail, excerpt
$custom_fields_str = '';
$builtin_fields_str = '';
$comments_str = '';

// Built-in Fields
if (isset(self::$data['post_type_defs'][$post_type]['supports']) && is_array(self::$data['post_type_defs'][$post_type]['supports'])) {
	if ( in_array('title', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$builtin_fields_str .= "\n\t<h1><?php the_title(); ?></h1>";
	}
	if ( in_array('editor', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$builtin_fields_str .= "\n\t\t<?php the_content(); ?>";
	}
	if ( in_array('author', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$builtin_fields_str .= "\n\t\t<?php the_author(); ?>";
	}
	if ( in_array('thumbnail', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$builtin_fields_str .= "\n\t\t<?php the_post_thumbnail(); ?>";
	}
	if ( in_array('excerpt', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$builtin_fields_str .= "\n\t\t<?php the_excerpt(); ?>";
	}
	if ( in_array('comments', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$comments_str .= "\n\t\t<?php comments_template(); ?>";
	}
}

// We show this for built-in types
elseif ($post_type == 'post') {
	$builtin_fields_str .= "\n\t<h1><?php the_title(); ?></h1>";
	$builtin_fields_str .= "\n\t\t<?php the_content(); ?>";
	$builtin_fields_str .= "\n\t\t<?php the_author(); ?>";
	$builtin_fields_str .= "\n\t\t<?php the_post_thumbnail(); ?>";
	$builtin_fields_str .= "\n\t\t<?php the_excerpt(); ?>";
	$comments_str .= "\n\t\t<?php comments_template(); ?>";
}
elseif ($post_type == 'page') {
	$builtin_fields_str .= "\n\t<h1><?php the_title(); ?></h1>";
	$builtin_fields_str .= "\n\t\t<?php the_content(); ?>";
	$builtin_fields_str .= "\n\t\t<?php the_author(); ?>";
	$builtin_fields_str .= "\n\t\t<?php the_post_thumbnail(); ?>";
}


// Custom fields
if ( isset(self::$data['post_type_defs'][$post_type]['custom_fields'])
	&& is_array(self::$data['post_type_defs'][$post_type]['custom_fields']) ) {
	foreach (  $def = self::$data['post_type_defs'][$post_type]['custom_fields'] as $cf ) {
		if (isset(self::$data['custom_field_defs'][$cf])) {

			$hide_from_templates = self::get_value(self::$data['custom_field_defs'][$cf], 'hide_from_templates');
			if ($hide_from_templates) {
				continue;
			}
			
			$filter = '';
			if (isset(self::$data['custom_field_defs'][$cf]['output_filter'])) {
				$filter = self::$data['custom_field_defs'][$cf]['output_filter'];
			}
			
			$filter_included = false; // until proven otherwise
			$filter_class = '';
			if (!empty($filter)) {
			
				$filter_class = CCTM::filter_prefix.$filter;
				if (!class_exists($filter_class)) {
					$filter_included = CCTM::load_file("/filters/$filter.php");
				}
			}
			
			// Show an example of the Output Filter
			if ($filter_included && $filter != 'raw') {				
				$OutputFilter = new $filter_class();
				$is_repeatable = false;
				if (isset(self::$data['custom_field_defs'][$cf]['is_repeatable'])) {
					$is_repeatable = self::$data['custom_field_defs'][$cf]['is_repeatable'];
				}
				$custom_fields_str .= sprintf("\t\t<strong>%s:</strong> %s<br />\n"
					, self::$data['custom_field_defs'][$cf]['label']
					, $OutputFilter->get_example(self::$data['custom_field_defs'][$cf]['name'], self::$data['custom_field_defs'][$cf]['type'], $is_repeatable)
				);
			}
			// Generic custom field usage
			else {
				$custom_fields_str .= sprintf("\t\t<strong>%s</strong> <?php print_custom_field('%s'); ?><br />\n"
					, self::$data['custom_field_defs'][$cf]['label'], self::$data['custom_field_defs'][$cf]['name']);
			}
		}
	}
}

// Reminder to the users
if (empty($custom_fields_str)) {
	$custom_fields_str = '<!-- '.__('You have not associated any custom fields with this post-type. Be sure to add any desired custom fields to this post-type by clicking on the "Manage Custom Fields" link under the Custom Content Type menu and checking the fields that you want.', CCTM_TXTDOMAIN).' -->';
}

// Populate placeholders
$hash['post_type'] = $post_type;
$hash['built_in_fields'] = $builtin_fields_str;
$hash['custom_fields'] = $custom_fields_str;
$hash['comments'] = $comments_str;

$data['single_page_sample_code'] = CCTM::parse($tpl, $hash, true);
//die('d.x.x.');
// include CCTM_PATH.'/views/sample_template.php';
$data['content'] = CCTM::load_view('sample_template.php', $data);
print CCTM::load_view('templates/default.php', $data);