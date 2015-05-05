<?php
/*------------------------------------------------------------------------------
Plugin Name: Custom Content Type Manager
Description: Break out of your blog: set up advanced Custom Post-Types and all kinds of custom fields, including dropdowns, checkboxes, and images. This makes WordPress into a true CMS.
Author: Everett Griffiths
Version: 0.9.7.13
Author URI: http://www.craftsmancoding.com/
Plugin URI: http://code.google.com/p/wordpress-custom-content-type-manager/
------------------------------------------------------------------------------*/
require_once 'includes/constants.php';
include_once 'includes/functions.php';

spl_autoload_register(function($class) {
    $prefix = 'CCTM\\';
    if (substr($class, 0, strlen($prefix)) == $prefix) {
        $class = substr($class, strlen($prefix));
    }
    // So our namespaces correspond to our folder structure
    $file = dirname(__FILE__).'/src/'.str_replace('\\', '/', $class).'.php';

    // Allow the user directory to provide overrides
    if (false) {
        // ... TODO ...    
    }
    elseif (is_readable($file)) {
        require_once $file;
    }
},false);

// Simple lazy-loading Dependency Injection Container
// Register any classes that are or have dependencies...
// basically anything that needs logging should be here.
$container = new Pimple();
$container['CCTM'] = function ($c) { return new \CCTM\CCTM($c); };
$container['Log'] = function ($c) {
    $c['log_level'] = (defined('CCTM_LOG_LEVEL')) ? CCTM_LOG_LEVEL : 1; 
    $c['log_function'] = 'error_log';
    $c['log_target'] = (defined('CCTM_LOG')) ? CCTM_LOG : false;
    return new \CCTM\Log($c); 
};
$container['File'] = function ($c) { return new \CCTM\File($c); };
$container['Dir'] = function ($c) { return new \CCTM\Dir($c); };
$container['Route'] = function ($c) {
    $c['create_nonce_function'] = 'wp_create_nonce';
    $c['check_nonce_function'] = 'wp_verify_nonce'; 
    $c['POST'] = $_POST;
    $c['GET'] = $_GET;
    return new \CCTM\Route($c); 
};
$container['get_option_function'] = 'get_option';
$container['update_option_function'] = 'update_option';
$container['GetPostsQuery'] = function ($c) { return new \CCTM\GetPostsQuery($c); };
$container['Pagination'] = function ($c) { return new \CCTM\Pagination($c); };
$container['Load'] = function ($c) { return new \CCTM\Load($c); };
$container['Cache'] = function ($c) { return new \CCTM\Cache($c); };
//$container['Model'] = $container->factory(function ($c) { return new \CCTM\Models\Model($c); });
$container['View'] = function ($c) { return new \CCTM\View($c); };
//$container['Controller'] = $container->factory(function ($c) { return new \CCTM\Controller($c); });
$container['Data'] = function ($c) { return new \CCTM\Data($c); };

// Run tests when the plugin is activated.
register_activation_hook(__FILE__, '\CCTM\Selfcheck::run');

//register setting
add_action('admin_init', function(){ 
    CCTM\License::register_option();
    CCTM\License::activate();
});

// Register Ajax Controllers (easier to hard-code than do scan dirs)
// pattern is: 'wp_ajax_{file-basename}', CCTM\Ajax::{file-basename}
add_action('wp_ajax_bulk_add',                  '\CCTM\Ajax::bulk_add');
add_action('wp_ajax_download_def',              '\CCTM\Ajax::download_def');
add_action('wp_ajax_format_getpostsquery_args', '\CCTM\Ajax::format_getpostsquery_args');
add_action('wp_ajax_get_posts',                 '\CCTM\Ajax::get_posts');
add_action('wp_ajax_get_search_form',           '\CCTM\Ajax::get_search_form');
add_action('wp_ajax_get_selected_posts',        '\CCTM\Ajax::get_selected_posts');
add_action('wp_ajax_get_shortcode',             '\CCTM\Ajax::get_shortcode');
add_action('wp_ajax_get_tpl',                   '\CCTM\Ajax::get_tpl');
add_action('wp_ajax_get_validator_options',     '\CCTM\Ajax::get_validator_options');
add_action('wp_ajax_get_widget_post_tpl',       '\CCTM\Ajax::get_widget_post_tpl');
add_action('wp_ajax_list_custom_fields',        '\CCTM\Ajax::list_custom_fields');
add_action('wp_ajax_post_content_widget',       '\CCTM\Ajax::post_content_widget');
add_action('wp_ajax_post_content_widget',       '\CCTM\Ajax::post_content_widget');
add_action('wp_ajax_preview_def',               '\CCTM\Ajax::preview_def');
add_action('wp_ajax_summarize_posts_form',      '\CCTM\Ajax::summarize_posts_form');
add_action('wp_ajax_summarize_posts_get_args',  '\CCTM\Ajax::summarize_posts_get_args');
add_action('wp_ajax_summarize_posts_widget',    '\CCTM\Ajax::summarize_posts_widget');
add_action('wp_ajax_upload_image',              '\CCTM\Ajax::upload_image');


// Load up the textdomain(s) for translations
CCTM\Load::file('/config/lang.php');
CCTM\CCTM::$license = CCTM\License::check();

$CCTM = $container['CCTM']; // Bang.

// Generate admin menu, bootstrap CSS/JS
add_action('admin_init', '\CCTM\CCTM::admin_init');

// Create custom plugin settings menu
add_action('admin_menu', function() { return \CCTM\Load::file('/config/menu.php'); });

// This shall remain anonymous until I find a way to test it
add_filter('plugin_action_links', function ($links, $file) {
	if ( $file == basename(dirname(__FILE__)).'/'.basename(__FILE__)) {
		$settings_link = sprintf('<a href="%s"><img src="%s"/>%s</a>'
			, admin_url( 'admin.php?page=cctm' )
			, CCTM_URL.'/images/icons/16x16/advanced.png'
			, __('Settings')
		);
		array_unshift( $links, $settings_link );
	}
	return $links;
}, 10, 2 );

if (CCTM\CCTM::$license!='valid') return;


// Load up the CCTM data from wp_options, populates CCTM::$data

//$CCTM->load_data();
//return;
CCTM\CCTM::load_data();

// Shortcodes
add_shortcode('summarize-posts', 'CCTM\SummarizePosts::get_posts');
add_shortcode('summarize_posts', 'CCTM\SummarizePosts::get_posts');
add_shortcode('custom_field', 'CCTM\CCTM::custom_field');
add_shortcode('cctm_post_form', 'CCTM\CCTM::cctm_post_form');

// Summarize Posts Tiny MCE button
if (CCTM\CCTM::get_setting('summarizeposts_tinymce')) {
	add_filter('mce_external_plugins', 'CCTM\SummarizePosts::tinyplugin_register');
	add_filter('mce_buttons', 'CCTM\SummarizePosts::tinyplugin_add_button', 0);
}
// Custom Fields Tiny MCE button
if (CCTM\CCTM::get_setting('custom_fields_tinymce')) {
	add_filter('mce_external_plugins', 'CCTM\CCTM::tinyplugin_register');
	add_filter('mce_buttons', 'CCTM\CCTM::tinyplugin_add_button', 0);
}

// Run any updates for this version.
add_action('init', 'CCTM\CCTM::check_for_updates', 0 );


// Register any custom post-types (a.k.a. content types)
add_action('init', 'CCTM\CCTM::register_custom_post_types', 11 );
add_action('widgets_init', 'CCTM\SummarizePosts_Widget::register_this_widget');
add_action('widgets_init', 'CCTM\PostWidget::register_this_widget');

if ( is_admin()) {
	// Generate admin menu, bootstrap CSS/JS
	//add_action('admin_init', 'CCTM::admin_init');

	// Create custom plugin settings menu
	//add_action('admin_menu', 'CCTM::create_admin_menu');
	//add_filter('plugin_action_links', 'CCTM::add_plugin_settings_link', 10, 2 );

	// Standardize Fields
	add_action('do_meta_boxes', 'CCTM\StandardizedCustomFields::remove_default_custom_fields', 10, 3 );
	add_action('add_meta_boxes', 'CCTM\StandardizedCustomFields::create_meta_box' );
	add_action('save_post', 'CCTM\StandardizedCustomFields::save_custom_fields', 1, 2 ); //! TODO: register this action conditionally

	// Customize the page-attribute box for custom page hierarchies
	add_filter('wp_dropdown_pages', 'CCTM\StandardizedCustomFields::customized_hierarchical_post_types', 100, 1);

	// FUTURE: Highlght which themes are CCTM-compatible (if any)
	// add_filter('theme_action_links', 'CCTM::highlight_cctm_compatible_themes');
	add_action('admin_notices', 'CCTM\CCTM::print_warnings');

	// Used to modify the large post icon
	add_action('in_admin_header', 'CCTM\StandardizedCustomFields::print_admin_header');

	// Handle Custom Columns: this is only relevant for the edit.php?post_type=xxxx pages (i.e. the list view)
	if ( substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1) == 'edit.php' ) {
		$post_type = CCTM\CCTM::get_value($_GET, 'post_type');

		if (isset(CCTM\CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns_enabled'])
			&& CCTM\CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns_enabled'] == 1
			&& isset(CCTM\CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns'])
			&& !empty(CCTM\CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns']) ) {

			// Draw the column headers
			add_filter("manage_{$post_type}_posts_columns" , 'CCTM\Columns::'.$post_type);

			// Handle the data in each cell
			add_action('manage_posts_custom_column', 'CCTM\Columns::populate_custom_column_data');
			add_action('manage_pages_custom_column', 'CCTM\Columns::populate_custom_column_data');

			// Forces custom post types to sort correctly
			add_filter('posts_orderby', 'CCTM\CCTM::order_posts');
			add_filter('posts_join', 'CCTM\CCTM::posts_join');
			
		}
	}
	
    add_filter('media_upload_tabs', 'CCTM\CCTM::customize_upload_tabs');
}

// Enable archives for custom post types
add_filter('request', 'CCTM\CCTM::request_filter');


// Modifies the "Right Now" widget
add_action('right_now_content_table_end' , 'CCTM\CCTM::right_now_widget');

// Handle Ajax Requests
add_action('wp_ajax_get_search_form', 'CCTM\CCTM::get_search_form');

// Needs to be first in priority (before WP) so we can look for any slashes indicating hierarchical post-types
add_filter('sanitize_title', 'CCTM\CCTM::filter_sanitize_title', 1, 3);

/*EOF*/