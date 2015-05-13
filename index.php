<?php
if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed');
/*------------------------------------------------------------------------------
Plugin Name: Custom Content Type Manager : Advanced Custom Post Types
Description: Allows users to create custom post types and custom fields, including dropdowns, checkboxes, and images. This gives WordPress CMS functionality making it easier to use WP for eCommerce or content-driven sites.
Author: Everett Griffiths
Version: 2.0
Author URI: https://www.craftsmancoding.com/
Plugin URI: https://github.com/craftsmancoding/custom-content-type-manager
------------------------------------------------------------------------------*/
define('CCTM_PATH', dirname(__FILE__ ));
define('CCTM_URL', plugins_url() .'/'. rawurlencode(basename(CCTM_PATH)) );
register_activation_hook(__FILE__, 'cctm_run_tests');
load_plugin_textdomain('cctm', false, basename(CCTM_PATH).'/lang/' );

function cctm_cannot_load()
{
    print '<div id="custom-post-type-manager-warning" class="error fade"><p><strong>'
        .__('The Custom Post Type Manager plugin requires PHP version 5.3.0 or greater','cctm')
        .'</strong> '
        .'</div>';
}

function cctm_run_tests()
{
    // Do more thorough tests here, e.g.
    // upload + temp directories exist and are writeable?
    // if( ! file_exists( $user_dirname ) )
    // wp_mkdir_p( $user_dirname );
    // if ( @is_dir( $temp ) && wp_is_writable( $temp ) )
    // scandir
    // json_encode
    // etc.
    // These are NOT unit tests
}

// Test for the bare minimums...
// These tests happen on EVERY page request, so best to keep it light.
if ( version_compare( phpversion(), '5.3.0', '<') ) {
    add_action('admin_notices', 'cctm_cannot_load');
    return; // exit
}


// Proceed with PHP 5.3.0 compatible stuff
require_once 'vendor/autoload.php';
require_once 'loader.php';
if ( is_admin())
{
    require_once 'admin-loader.php';
}


/*EOF*/
