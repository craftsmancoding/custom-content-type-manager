<?php
if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed');
/**
 * This file (loader.php) is called only when we've checked for any potential
 * conflicts with function names, class names, or constant names. With so many WP
 * plugins available and so many potential conflicts out there, I've attempted to
 * avoid the headaches caused from name pollution as much as possible.
 *
 * @package
 */


/*
Run tests only upon activation
http://codex.wordpress.org/Function_Reference/register_activation_hook
*/

require_once 'includes/CCTM.php';
require_once 'includes/constants.php';
require_once 'includes/SummarizePosts.php';
require_once 'includes/GetPostsQuery.php';
require_once 'includes/SummarizePosts_Widget.php';
require_once 'includes/CCTM_Post_Widget.php';
require_once 'includes/StandardizedCustomFields.php';
require_once 'includes/CCTM_FormElement.php';
require_once 'includes/CCTM_Ajax.php';
require_once 'includes/functions.php';

CCTM::$Ajax = new CCTM_Ajax();

// Load up the textdomain(s) for translations
CCTM::load_file('/config/lang/dictionaries.php');

// Get admin ready, print any CCTMtests::$errors in the admin dashboard
add_action( 'admin_notices', 'CCTM::print_notices');


if (empty(CCTM::$errors)) {
	// Load up the CCTM data from wp_options, populates CCTM::$data
	CCTM::load_data();

	// Shortcodes
	add_shortcode('summarize-posts', 'SummarizePosts::get_posts');
	add_shortcode('summarize_posts', 'SummarizePosts::get_posts');
	add_shortcode('custom_field', 'CCTM::custom_field');
	add_shortcode('cctm_post_form', 'CCTM::cctm_post_form');

	// Summarize Posts Tiny MCE button
	if (CCTM::get_setting('summarizeposts_tinymce')) {
		add_filter('mce_external_plugins', 'SummarizePosts::tinyplugin_register');
		add_filter('mce_buttons', 'SummarizePosts::tinyplugin_add_button', 0);
	}
	// Custom Fields Tiny MCE button
	if (CCTM::get_setting('custom_fields_tinymce')) {
		add_filter('mce_external_plugins', 'CCTM::tinyplugin_register');
		add_filter('mce_buttons', 'CCTM::tinyplugin_add_button', 0);
	}

	// Run any updates for this version.
	add_action('init', 'CCTM::check_for_updates', 0 );


	// Register any custom post-types (a.k.a. content types)
	add_action('init', 'CCTM::register_custom_post_types', 11 );
	add_action('widgets_init', 'SummarizePosts_Widget::register_this_widget');
	add_action('widgets_init', 'CCTM_Post_Widget::register_this_widget');

	if ( is_admin()) {
		// Generate admin menu, bootstrap CSS/JS
		add_action('admin_init', 'CCTM::admin_init');

		// Create custom plugin settings menu
		add_action('admin_menu', 'CCTM::create_admin_menu');
		add_filter('plugin_action_links', 'CCTM::add_plugin_settings_link', 10, 2 );

		// Standardize Fields
		add_action('do_meta_boxes', 'StandardizedCustomFields::remove_default_custom_fields', 10, 3 );
		add_action('add_meta_boxes', 'StandardizedCustomFields::create_meta_box' );
		add_action('save_post', 'StandardizedCustomFields::save_custom_fields', 1, 2 ); //! TODO: register this action conditionally

		// Customize the page-attribute box for custom page hierarchies
		add_filter('wp_dropdown_pages', 'StandardizedCustomFields::customized_hierarchical_post_types', 100, 1);

		// FUTURE: Highlght which themes are CCTM-compatible (if any)
		// add_filter('theme_action_links', 'CCTM::highlight_cctm_compatible_themes');
		add_action('admin_notices', 'CCTM::print_warnings');

		// Used to modify the large post icon
		add_action('in_admin_header', 'StandardizedCustomFields::print_admin_header');

		// Handle Custom Columns: this is only relevant for the edit.php?post_type=xxxx pages (i.e. the list view)
		if ( substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1) == 'edit.php' ) {
			$post_type = CCTM::get_value($_GET, 'post_type');

			if (isset(CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns_enabled'])
				&& CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns_enabled'] == 1
				&& isset(CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns'])
				&& !empty(CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns']) ) {

				require_once 'includes/CCTM_Columns.php';
				require_once 'includes/functions.php';
				CCTM::$Columns = new CCTM_Columns();
				CCTM::$Columns->post_type = $post_type;

				// Draw the column headers
				add_filter("manage_{$post_type}_posts_columns" , array(CCTM::$Columns, $post_type));

				// Handle the data in each cell
				add_action('manage_posts_custom_column', array(CCTM::$Columns, 'populate_custom_column_data'));
				add_action('manage_pages_custom_column', array(CCTM::$Columns, 'populate_custom_column_data'));

				// Forces custom post types to sort correctly
				add_filter('posts_orderby', 'CCTM::order_posts');
				add_filter('posts_join', 'CCTM::posts_join');

			}
		}

        add_filter('media_upload_tabs', 'CCTM::customize_upload_tabs');
	}

	// Enable archives for custom post types
	//add_filter('request', 'CCTM::request_filter');


	// Modifies the "Right Now" widget
	add_action('right_now_content_table_end' , 'CCTM::right_now_widget');

	// Handle Ajax Requests
	add_action('wp_ajax_get_search_form', 'CCTM::get_search_form');

	// Needs to be first in priority (before WP) so we can look for any slashes indicating hierarchical post-types
	add_filter('sanitize_title', 'CCTM::filter_sanitize_title', 1, 3);


}

/*EOF*/