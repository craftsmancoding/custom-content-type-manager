<?php
if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed');
/**
 * This file is only called when we've guaranteed PHP 5.3.0 or greater.
 * Loaded only when we're within the admin dashboard (does NOT mean the current user is admin)
 */
use Windwalker\Renderer\BladeRenderer;
use CCTM\Controller\AdminController;
use CCTM\Controller\AjaxController;

$upload_dir = wp_upload_dir();
$upload_dir = $upload_dir['basedir'].'/cctm';




add_action('admin_init', function(){
    $file = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1);
    $page = (isset($_GET['page'])) ? $_GET['page'] : '';

    // Only add our junk if we are creating/editing a post or we're on
    // on of our CCTM pages
    if ( in_array($file, array('post.php', 'post-new.php', 'edit.php', 'widgets.php')) || preg_match('/^cctm.*/', $page) ) {

        //print 'asdfasdf'; exit;
//        wp_register_style('CCTM_css', CCTM_URL . '/css/manager.css');
//        wp_enqueue_style('CCTM_css');
        // Hand-holding: If your custom post-type omits the main content block,
        // then thickbox will not be queued and your image, reference, selectors will fail.
        // Also, we have to fix the bugs with WP's thickbox.js, so here we include a patched file.
//        wp_register_script('cctm_thickbox', CCTM_URL . '/js/thickbox.js', array('thickbox') );
//        wp_enqueue_script('cctm_thickbox');
//        wp_enqueue_style('thickbox');

//        wp_enqueue_style('jquery-ui-tabs', CCTM_URL . '/css/smoothness/jquery-ui-1.8.11.custom.css');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-dialog');



        // The following makes PHP variables available to Javascript the "correct" way.
        wp_register_script('cctm_js', 'assets/js/cctm.js' );
        wp_localize_script('cctm_js', 'cctm', array(
            'url' => CCTM_URL,
            'nonce' => wp_create_nonce('cctm_nonce')
        ));

        // Enqueued script with localized data.
        wp_enqueue_script('cctm_js');

        //wp_enqueue_script('angular', CCTM_URL . '/assets/components/angular/angular.min.js' );
        //wp_enqueue_script('angular-animate', CCTM_URL . '/assets/components/angular-animate/angular-animate.min.js' );
        //wp_enqueue_script('angular-route', CCTM_URL . '/assets/components/angular-route/angular-route.min.js' );
    }

    // Allow each custom field to load up any necessary CSS/JS.
//    self::initialize_custom_fields();

});

// Main menu item
add_action('admin_menu', function() {
    global $upload_dir;
    $paths = new \SplPriorityQueue;
    $paths->insert(CCTM_PATH.'/views', 100);
    $paths->insert($upload_dir.'/cctm/views', 200);

    wp_enqueue_script('angular', CCTM_URL . '/assets/components/angular/angular.js' );
    wp_enqueue_script('angular-animate', CCTM_URL . '/assets/components/angular-animate/angular-animate.js' );
    wp_enqueue_script('angular-route', CCTM_URL . '/assets/components/angular-route/angular-route.js' );
    wp_enqueue_script('cctm-app', CCTM_URL . '/app/app.module.js' );
    wp_enqueue_script('cctm-app.main', CCTM_URL . '/app/components/main/main.js' );
    wp_enqueue_script('cctm-app.settings', CCTM_URL . '/app/components/settings/settings.js' );

    //wp_enqueue_script('cctm-routes', CCTM_URL . '/app/app.routes.js' );

    add_menu_page(
        __('Manage Custom Content Types', 'cctm'),  // page title
        __('Custom Content Types', 'cctm'),     // menu title
        'manage_options',						// capability
        'cctm',								    // menu-slug (should be unique)
        array(
            new AdminController(
                //new BladeRenderer(array(CCTM_PATH.'/views',$upload_dir.'/views'), array('cache_path' => get_temp_dir()))
                new BladeRenderer($paths, array('cache_path' => get_temp_dir()))
            ),
            'getIndex'
        ), // callback function
        // see https://developer.wordpress.org/resource/dashicons/#media-code
        CCTM_URL .'/assets/images/gear.png',           // Icon
        73					                    // menu position
    );
});

// All CCTM Ajax requests will use action = 'cctm'.
// Further routing will be done internally in the AjaxController class
add_action( 'wp_ajax_cctm', array(new AjaxController(), 'getIndex'));


/*EOF*/