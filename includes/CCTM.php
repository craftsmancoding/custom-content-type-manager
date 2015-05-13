<?php
/**
 * CCTM  Custom Content Type Manager
 * 
 * This is the main class for the Custom Content Type Manager plugin.
 * It holds its functions hooked to WP events and utilty functions and configuration
 * settings.
 * 
 * Homepage:
 * http://code.google.com/p/wordpress-custom-content-type-manager/
 * 
 * This plugin handles the creation and management of custom post-types (also
 * referred to as 'content-types').
 * 
 * @package cctm
 */
class CCTM {
	// Name of this plugin and version data.
	// See http://php.net/manual/en/function.version-compare.php:
	// any string not found in this list < dev < alpha =a < beta = b < RC = rc < # < pl = p
	const name   = 'Custom Content Type Manager';
    const version = '0.9.8.6';
	const version_meta = 'pl'; // dev, rc (release candidate), pl (public release)

	// Required versions (referenced in the CCTMtest class).
	const wp_req_ver  = '3.3';
	const php_req_ver  = '5.2.6';
	const mysql_req_ver = '4.1.2';

	/**
	 * The following constants identify the option_name in the wp_options table
	 * where this plugin stores various data.
	 */
	const db_key  = 'cctm_data';

	/**
	 * Determines where the main CCTM menu appears. WP is vulnerable to conflicts
	 * with menu items, so the parameter is listed here for easier editing.
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=203
	 */
	const menu_position = 73;

	// Each class that extends either the CCTM_FormElement class or the
	// the CCTM_OutputFilter class must prefix this to its class name.
	const field_prefix = 'CCTM_';
	const filter_prefix = 'CCTM_';
	const validator_prefix = 'CCTM_Rule_';
	
	// used to control the uploading of the .cctm.json files
	const max_def_file_size = 524288; // in bytes

	// Directory relative to wp-content/uploads/ where we can store def files
	// Omit the trailing slash.
	const base_storage_dir = 'cctm';

	/**
	 * Directory relative to wp-content/uploads/{self::base_storage_dir} used to store
	 * the .cctm.json definition files. Omit the trailing slash.
	 */
	const def_dir = 'defs';



	/**
	 * Directory relative to wp-content/uploads/{self::base_storage_dir} used to store
	 * any 3rd-party or custom custom field types. Omit the trailing slash.
	 */
	const custom_fields_dir = 'fields';


	/**
	 * Directory relative to wp-content/uploads/{self::base_storage_dir} used to store
	 * formatting templates (tpls)
	 * May contain the following sub directories: fields, fieldtypes, metaboxes
	 */
	const tpls_dir = 'tpls';

	// Default permissions for dirs/files created in the base_storage_dir.
	// These cannot be more permissive thant the system's settings: the system
	// will automatically shave them down. E.g. if the system has a global setting
	// of 0755, a local setting here of 0770 gets bumped down to 0750.
	const new_dir_perms = 0755;
	const new_file_perms = 0644;

	//------------------------------------------------------------------------------
	/**
	 * This contains the CCTM_Ajax object, stashed here for easy reference.
	 */
	public static $Ajax;
	/**
	 * Contains the CCTM_Columns object, used for custom columns.
	 */
	public static $Columns;

	// Used to filter settings inputs (e.g. descriptions of custom fields or post-types)
	public static $allowed_html_tags = '<a><strong><em><code><style>';

	// Data object stored in the wp_options table representing all primary data
	// for post_types and custom fields
	public static $data = array();

	// Cached data: for a single request, e.g. custom field values.
	public static $cache = array();

	// integer iterator used to uniquely identify groups of field definitions for
	// CSS and $_POST variables
	public static $def_i = 0;

    public static $hide_url_tab = false;
    
	// Optionally used for shortcodes
	public static $post_id = null;
	
	// This is the definition shown when a user first creates a post_type
	public static $default_post_type_def = array
	(
		'supports' => array('title', 'editor'),
		'taxonomies' => array(),
		'post_type' => '',
		'labels' => array
		(
			'menu_name' => '',
			'singular_name' => '',
			'add_new' => '',
			'add_new_item' => '',
			'edit_item' => '',
			'new_item' => '',
			'view_item' => '',
			'search_items' => '',
			'not_found' => '',
			'not_found_in_trash' => '',
			'parent_item_colon' => '',
		),
		'description' => '',
		'show_ui' => 1,
		'public' => 1, // 0.9.4.2 tried to set this verbosely, but WP still req's this attribute
		'menu_icon' => '',
		'label' => '',
		'menu_position' => '',
		'show_in_menu' => 1,
		'cctm_show_in_menu' => 1,
		
		'rewrite_with_front' => 1,
		'permalink_action' => 'Off',
		'rewrite_slug' => '',
		'show_in_admin_bar' => 1,
		'query_var' => '',
		'capability_type' => 'post',
		'capabilities' => '',
		'register_meta_box_cb' => '',
		'map_meta_cap' => 0,
		'show_in_nav_menus' => 1,
		'publicly_queryable' => 1,
		'include_in_search' => 1, // this makes more sense to users than the exclude_from_search,
		'exclude_from_search' => 0, // but this is what register_post_type expects. Boo.
		'include_in_rss' => 1,  // this is a custom option.. should use 'cctm' prefix. Oops.
		'can_export' => 1,
		'use_default_menu_icon' => 1,
		'hierarchical' => 0,
		'rewrite' => '',
		'has_archive' => 0,
		'custom_order' => 'ASC',
		'custom_orderby' => '',
		'cctm_custom_columns_enabled' => 0,
		'cctm_enable_right_now' => 1
	);

	/**
	 * List default global settings here. (see controllers/settings.php)
	 */
	public static $default_settings = array(
		'delete_posts' => 0
		, 'delete_custom_fields' => 0
		, 'add_custom_fields' => 0
		, 'show_custom_fields_menu' => 1
		, 'show_settings_menu' => 1
		, 'show_foreign_post_types' => 1
		, 'cache_directory_scans' => 1
		, 'cache_thumbnail_images' => 0
		, 'save_empty_fields' => 1
		, 'summarizeposts_tinymce' => 1
		, 'custom_fields_tinymce' => 1
		, 'pages_in_rss_feed'	=> 0
		, 'enable_right_now'	=> 1
		, 'hide_posts'	=> 0
		, 'hide_pages'	=> 0
		, 'hide_links'	=> 0
		, 'hide_comments' => 0
	);

	// Default metabox definition
	public static $metabox_def = array(
		'id' => 'cctm_default',
		'title' => 'Custom Fields',
		'context' => 'normal',
		'priority' => 'default',
		'post_types' => array(),
		'callback' => '',
		'callback_args' => '',
		'visibility_control' => '',
	);

	// Where are the icons for custom images stored?
	// TODO: let the users select their own dir in their own directory
	public static $custom_field_icons_dir;

	// Built-in post-types that can have custom fields, but cannot be deleted.
	public static $built_in_post_types = array('post', 'page');

	// Names that are off-limits for custom post types b/c they're already used by WP
	// Re "preview" see http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=321
	public static $reserved_post_types = array('post', 'page', 'attachment', 'revision'
		, 'nav_menu', 'nav_menu_item', 'preview','portfolio');
	
	// Any post-types that WP registers, but which the CCTM should ignore (can't have custom fields)
	public static $ignored_post_types = array('attachment', 'revision', 'nav_menu', 'nav_menu_item');

	// Custom field names are not allowed to use the same names as any column in wp_posts
	public static $reserved_field_names = array('ID', 'post_author', 'post_date', 'post_date_gmt',
		'post_content', 'post_title', 'post_excerpt', 'post_status', 'comment_status', 'ping_status',
		'post_password', 'post_name', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt',
		'post_content_filtered', 'post_parent', 'guid', 'menu_order', 'post_type', 'post_mime_type',
		'comment_count');

	// Future-proofing: post-type names cannot begin with 'wp_'
	// See: http://codex.wordpress.org/Custom_Post_Types
	// FUTURE: List any other reserved prefixes here (if any)
	public static $reserved_prefixes = array('wp_');

	/**
	 * Warnings are stored as a simple array of text strings, e.g. 'You spilled your coffee!'
	 * Whether or not they are displayed is determined by checking against the self::$data['warnings']
	 * array: the text of the warning is hashed and this is used as a key to identify each warning.
	 */
	public static $warnings = array();



	/**
	 * used to store some validation errors or serious problems. The errors take this format:
	 * self::$errors['field_name'] = 'Description of error';
	 */
	public static $errors;

	/**
	 * Used by the "required" fields and any custom validations on post/page fields.
	 */
	public static $post_validation_errors;

	/**
	 * Used for search parameters
	 */
	public static $search_by = array();

	/**
	 * Used by the image, media, relation post-selector.
	 */
	public static $post_selector = array();

	//! Private Functions
	//------------------------------------------------------------------------------
	/**
	 * Returns a URL to a thumbnail image.  Attempts to create and cache the image;
	 * we just return the path to the full-sized image if we fail to cache it (which
	 * is what WP does.
	 * See See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=256
	 * 
	 * @param array $p post array from WP's get_post ARRAY_A
	 * @return string	thumbnail_url
	 */
	private static function _get_create_thumbnail($p) {
		// Custom handling of images. 
		$WIDTH = 32;
		$HEIGHT = 32;
		$QUALITY = 100;


		// Base image cache dir: our jumping off point.
		$cache_dir = CCTM_3P_PATH .'/cache/images/';
		$info = pathinfo($p['guid']);
		//$ext = '.'.$info['extension'];
		$ext = '.jpg';
		
		$hash_id = md5($p['guid'].$WIDTH.$HEIGHT.$QUALITY); //
		//$hash_id = md5(print_r($p,true).$WIDTH.$HEIGHT.$QUALITY); //
		
		// atomize our image so we don't overload our directories (shell wildcards)
		// See http://drupal.org/node/171444 for one example of this common problem
		$subdir_array = str_split($hash_id);
		$filename = array_pop($subdir_array); // the last letter
		$subdir = implode('/', $subdir_array); // e.g. a/b/c/1/5/e
		// The image location is relative to the cache/images directory
		$image_location = $subdir.'/'.$filename.$ext; // e.g. a/b/c/1/5/e/f.jpg
		
		
		$thumbnail_path = CCTM_3P_PATH .'/cache/images/'.$image_location;
		$thumbnail_url = CCTM_3P_URL .'/cache/images/'.$image_location;


		// If it's already there, we're done
		if (file_exists($thumbnail_path)) {
			return $thumbnail_url;
		}


		// If it's not there, we must create it.
		if (!file_exists($cache_dir.$subdir) && !mkdir($cache_dir.$subdir, 0777, true)) {

			// Notify the user
			CCTM::$errors['could_not_create_cache_dir'] = sprintf(
				__('Could not create the cache directory at %s.', CCTM_TXTDOMAIN)
				, "<code>$cache_dir</code>. Please create the directory with permissions so PHP can write to it.");

            CCTM::log('Failed to create directory '.$cache_dir.$subdir, __FILE__,__LINE__);

			// Failed to create the dir... now what?!?  We cram the full-sized image into the 
			// small image tag, which is exactly what WP does (yes, seriously.)				
			return $p['guid'];
				
		}
		
		// the cache directory exits; create the cached image
		require_once(CCTM_PATH.'/includes/CCTM_SimpleImage.php');
		$image = new CCTM_SimpleImage();
		$image->load($p['guid']); // You may use the image URL
		$image->resize($WIDTH, $HEIGHT);
		if (!$image->save($thumbnail_path, IMAGETYPE_JPEG, $QUALITY)) {
			CCTM::$errors['could_not_create_img'] = sprintf(
				__('Could not create cached image: %s.', CCTM_TXTDOMAIN)
				, "<code>$thumbnail_path</code>");
            CCTM::log('Could not save the image '.$thumbnail_path,__FILE__,__LINE__);			
			return $p['guid'];
			
		}
		
		return $thumbnail_url;
	}
	
	
	//------------------------------------------------------------------------------
	/**
	 * Prepare a post type definition for registration: this translates the data structure
	 * as it is stored into how it needs to appear in order to be passed to the 
	 * register_post_type() function.
	 *
	 * @param array   the CCTM definition for a post type
	 * @param array $def
	 * @return mixed  the WordPress authorized definition format.
	 */
	private static function _prepare_post_type_def($def) {
		// Sigh... working around WP's irksome inputs
		if (isset($def['cctm_show_in_menu']) && $def['cctm_show_in_menu'] == 'custom') {
			$def['show_in_menu'] = $def['cctm_show_in_menu_custom'];
		}
		else {
			$def['show_in_menu'] = (bool) self::get_value($def, 'cctm_show_in_menu');
		}
		
		$def['hierarchical'] = (bool) self::get_value($def, 'hierarchical');
		
		// We display "include" type options to the user, and here on the backend
		// we swap this for the "exclude" option that the function requires.
		$include = self::get_value($def, 'include_in_search');

		if (empty($include)) {
			$def['exclude_from_search'] = true;
		}
		else {
			$def['exclude_from_search'] = false;
		}

		// TODO: retro-support... if public is checked, then the following options are inferred
		/*
		if (isset($def['public']) && $def['public']) {
			$def['publicly_queriable'] = true;
			$def['show_ui'] = true;
			$def['show_in_nav_menus'] = true;
			$def['exclude_from_search'] = false;
		}
		*/

		// Verbosely check to see if "public" is inferred
		if (isset($def['publicly_queriable']) && $def['publicly_queriable']
			&& isset($def['show_ui']) && $def['show_ui']
			&& isset($def['show_in_nav_menus']) && $def['show_in_nav_menus']
			&& (!isset($def['exclude_from_search']) || (isset($def['exclude_from_search']) && !$def['publicly_queriable']))
		) {
			$def['public'] = true;
		}
		
		// Provide default mapping if none are supplied verbosely.
		// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=409
		// http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=460
		if (empty($def['capabilities']) && isset($def['capability_type']) && !empty($def['capability_type'])) {
			$capability_type = $def['capability_type'];
			$def['capabilities'] = array(
			    'edit_post'              => "edit_{$capability_type}",
			    'read_post'              => "read_{$capability_type}",
			    'delete_post'            => "delete_{$capability_type}",
			    'edit_posts'             => "edit_{$capability_type}s",
			    'edit_others_posts'      => "edit_others_{$capability_type}s",
			    'publish_posts'          => "publish_{$capability_type}s",
			    'read_private_posts'     => "read_private_{$capability_type}s",
			    'delete_posts'           => "delete_{$capability_type}s",
			    'delete_private_posts'   => "delete_private_{$capability_type}s",
			    'delete_published_posts' => "delete_published_{$capability_type}s",
			    'delete_others_posts'    => "delete_others_{$capability_type}s",
			    'edit_private_posts'     => "edit_private_{$capability_type}s",
			    'edit_published_posts'   => "edit_published_{$capability_type}s",
			);
		}
		elseif (empty($def['capabilities'])) {
			unset($def['capabilities']);		
		}
		elseif(is_scalar($def['capabilities'])) {
			$capabilities = array();
			parse_str($def['capabilities'], $z);
			$def['capabilities'] = $capabilities;
		}
		// Ignore the capabilities mapping unless we have the map_meta_cap checked.
		// The map_meta_cap MUST be unset or a literal null.
		if (!CCTM::get_value($def,'map_meta_cap')) {
			unset($def['capabilities']);
			unset($def['map_meta_cap']);
		}

		// Allow for singular,plural capability_type's
		if (isset($def['capability_type'])) {				
			$tmp_capability_type = explode(',',$def['capability_type']);			
			if (count($tmp_capability_type) > 1) {
				array_walk($tmp_capability_type,'trim');
				$def['capability_type'] = $tmp_capability_type;
			}
		}
		unset($def['custom_orderby']);

        // https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=534
        if (isset($def['query_var']) && empty($def['query_var']))
        {
            unset($def['query_var']);
        }

		return $def;
	}


	//! Public Functions
	//------------------------------------------------------------------------------
	/**
	 * Load CSS and JS for admin folks in the manager.  Note that we have to verbosely
	 * ensure that thickbox's css and js are loaded: normally they are tied to the
	 * "editor" area of the content type, so thickbox would otherwise fail
	 * if your custom post_type doesn't use the main editor.
	 * See http://codex.wordpress.org/Function_Reference/wp_enqueue_script for a list
	 * of default scripts bundled with WordPress
	 */
	public static function admin_init() {

		$file = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1);
		$page = self::get_value($_GET, 'page');

		// Only add our junk if we are creating/editing a post or we're on
		// on of our CCTM pages
		if ( in_array($file, array('post.php', 'post-new.php', 'edit.php', 'widgets.php')) || preg_match('/^cctm.*/', $page) ) {

			wp_register_style('CCTM_css', CCTM_URL . '/css/manager.css');
			wp_enqueue_style('CCTM_css');
			// Hand-holding: If your custom post-type omits the main content block,
			// then thickbox will not be queued and your image, reference, selectors will fail.
			// Also, we have to fix the bugs with WP's thickbox.js, so here we include a patched file.
			wp_register_script('cctm_thickbox', CCTM_URL . '/js/thickbox.js', array('thickbox') );
			wp_enqueue_script('cctm_thickbox');
			wp_enqueue_style('thickbox');

			wp_enqueue_style('jquery-ui-tabs', CCTM_URL . '/css/smoothness/jquery-ui-1.8.11.custom.css');
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-dialog');

			wp_enqueue_script('cctm_manager', CCTM_URL . '/js/manager.js' );

			// The following makes PHP variables available to Javascript the "correct" way.
			// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=226
			$data = array();
			$data['cctm_url'] = CCTM_URL;
			$data['ajax_url'] = admin_url('admin-ajax.php');
			$data['ajax_nonce'] = wp_create_nonce('ajax_nonce');			
			wp_localize_script( 'cctm_manager', 'cctm', $data );

		}

		// Allow each custom field to load up any necessary CSS/JS.
		self::initialize_custom_fields();

	}


	//------------------------------------------------------------------------------
	/**
	 * Adds a link to the settings directly from the plugins page.  This filter is
	 * called for each plugin, so we need to make sure we only alter the links that
	 * are displayed for THIS plugin.
	 *
	 * INPUTS (determined by WordPress):
	 *   array('deactivate' => 'Deactivate')
	 * relative to the plugins directory, e.g. 'custom-content-type-manager/index.php'
	 *
	 * @param array   $links is a hash of links to display in the format of name => translation e.g.
	 * @param string  $file  is the path to plugin's main file (the one with the info header),
	 * @return array $links
	 */
	public static function add_plugin_settings_link($links, $file) {
		if ( $file == basename(dirname(dirname(__FILE__))) . '/index.php' ) {
			$settings_link = sprintf('<a href="%s">%s</a>'
				, admin_url( 'admin.php?page=cctm' )
				, __('Settings')
			);
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Prints a form to the end user so that the users on the front-end can create 
	 * posts (beware security!).
	 *
	 * [cctm_post_form post_type="property" thank_you_url="" on_save="default-action-here"]
	 *
	 * @param	array	$raw_args: parameters from the shortcode: name, filter
	 * @param	string	$options (optional)
	 * @return string (printed)	 
	 */
	public static function cctm_post_form($raw_args=array(), $options = null) {
		
		$post_type = CCTM::get_value($raw_args, 'post_type');
		$output = array(
			'content' => '',
			'errors' => '',
		);
		$defaults = array(
			'post_type' => '',
			'post_status' => 'draft', // for security!
			'post_author' => CCTM::get_user_identifier(),
			'post_date' => date('Y-m-d H:i:s'),
			'post_date_gmt' => gmdate('Y-m-d H:i:s'),
			'post_modified' => date('Y-m-d H:i:s'),
			'post_modified_gmt' => gmdate('Y-m-d H:i:s'),
			'_label_title' => __('Title'),
			'_label_content' => __('Content'),
			'_label_excerpt' => __('Excerpt'),			
			'_callback_pre' => null,
			'_callback' => 'CCTM::post_form_handler',
			'_action' => get_permalink(), // of current page
			'_tpl' => '_default',
			'_id_prefix' => 'cctm_',
			'_name_prefix' => 'cctm_',
			'_css' => CCTM_URL . '/css/manager.css,'.CCTM_URL.'/css/validation.css',
			'_fields' =>'',
			'_debug' => 0,
		);

		$args = array_merge($defaults, $raw_args );

		// Call the _callback_pre function (if present).
		if ($args['_callback_pre']) {
            if (function_exists($args['_callback_pre'])) {
                $output['content'] .= call_user_func($args['_callback_pre'], $args);
			}
			else {
                return '_callback_pre function does not exist: '.$args['_callback_pre'];
			}
		}
	
		// Load CSS
		$css = explode(',', $args['_css']);
		foreach ($css as $c) {
			$css_id = basename($c);
			wp_register_style($css_id, $c);
			wp_enqueue_style($css_id);
		}

		// Hard error
		if (empty($post_type)) {
			return __('cctm_post_form shortcode requires the "post_type" parameter.', CCTM_TXTDOMAIN);
		}
		// Disallow certain problematic post-types
		elseif (in_array($post_type, array('attachment','revision','nav_menu_item'))) {
			return sprintf(__('cctm_post_form shortcode does not support that post_type: %s', CCTM_TXTDOMAIN)
				,$post_type);
		}
		// WTF?
		if (!post_type_exists($post_type)) {
			return sprintf(__('cctm_post_form shortcode post_type not found: %s', CCTM_TXTDOMAIN)
				,$post_type);
		}
		
        //------------------------------
		// Process the form on submit
        //------------------------------		
		$nonce = CCTM::get_value($_POST, '_cctm_nonce');
		if ( !empty($_POST)) {

			// Bogus submission
			if (!wp_verify_nonce($nonce, 'cctm_post_form_nonce')) {
				die('Your form could not be submitted.  Please reload the page and try again.');
			}
			// Strip prefix from post keys: only collect those with the given prefix.
			// This should allow mulitiple forms on one page.
			$vals = array();
			foreach ($_POST as $k => $v) {
				if (preg_match('/^'.preg_quote($args['_name_prefix']).'/',$k)) {
					$k = preg_replace('/^'.preg_quote($args['_name_prefix']).'/', '', $k);
					$vals[$k] = wp_kses($v, array()); // TODO: options for this?
				}
			}

			// Validate fields
			StandardizedCustomFields::validate_fields($post_type,$vals);
			
			$vals = array_merge($vals,$args);
			
    		if ($args['_debug']) {
                print '<div style="background-color:orange; padding:10px;"><h3>[cctm_post_form] DEBUG MODE</h3>'
                    .'<h4>Posted Data</h4><pre>'.print_r($vals,true).'</pre>'
                    .'</div>';
                return;
    		}			
			
			// Save data if it was properly submitted	
			if (empty(CCTM::$post_validation_errors)) {
				return call_user_func($args['_callback'], $vals);
			}
			// Populate the main error block
			else {
				$error_item_tpl = CCTM::load_tpl(array('forms/_error_item.tpl'));
				$hash = array();
				$hash['errors'] = '';
				$hash['error_msg'] = __('This form has validation errors.', CCTM_TXTDOMAIN);
				$hash['cctm_url'] = CCTM_URL;
				foreach (CCTM::$post_validation_errors as $k => $v) {
					$hash['errors'] .= CCTM::parse($error_item_tpl, array('error'=>$v));				
				}
				$error_wrapper_tpl = CCTM::load_tpl(array('forms/_error_wrapper.tpl'));
				$output['errors'] = CCTM::parse($error_wrapper_tpl, $hash);
			}
        }
        
        //------------------------------
		// Generate the form.        		
        //------------------------------
		$output['_action'] = $args['_action'];

		// Custom fields	
		$explicit_fields = false;	
		$custom_fields = array();		
		if ($args['_fields']) {
            $explicit_fields = true;
            $tmp = explode(',', $args['_fields']);
            foreach ($tmp as $t) {
                $custom_fields[] = trim($t);
            }
            $args['_fields'] = $custom_fields;
		}
		elseif (isset(CCTM::$data['post_type_defs'][$post_type]['custom_fields'])) {
		  $custom_fields = CCTM::$data['post_type_defs'][$post_type]['custom_fields'];
		}


		// Post Title
		if ( 
		  !$explicit_fields && post_type_supports($args['post_type'],'title') && !isset($args['post_title']) 
		  || ($explicit_fields && in_array('post_title', $custom_fields)) 
        ) {
            
        	$FieldObj = CCTM::load_object('text','fields');
			$post_title_def = array(
				'label' => $args['_label_title'],
				'name' => 'post_title',
				'default_value' => wp_kses(
					CCTM::get_value($_POST,$args['_name_prefix'].'post_title'),array()),
				'extra' => '',
				'class' => '',
				'description' => '',
				'validator' => '',
				'output_filter' => '',
				'type' => 'text'
			);
			$FieldObj->set_props($post_title_def);
            $output_this_field = $FieldObj->get_create_field_instance();
			$output['post_title'] = $output_this_field;
			$output['content'] .= $output_this_field;
		}
		
		// Post Content (editor)
		if (
            !$explicit_fields && post_type_supports($args['post_type'],'editor') && !isset($args['post_content'])
            || ($explicit_fields && in_array('post_content', $custom_fields)) 
        ) {
        	$FieldObj = CCTM::load_object('textarea','fields'); // TODO: change to wysiwyg
			$post_title_def = array(
				'label' => $args['_label_content'],
				'name' => 'post_content',
				'default_value' => wp_kses(
					CCTM::get_value($_POST,$args['_name_prefix'].'post_content'),array()),
				'extra' => 'cols="80" rows="10"',
				'class' => '',
				'description' => '',
				'validator' => '',
				'output_filter' => '',
				'type' => 'textarea' // TODO: implement simplified WYSIWYG
			);
			$FieldObj->set_props($post_title_def);
			    $output_this_field = $FieldObj->get_create_field_instance();
			$output['post_content'] = $output_this_field;
			$output['content'] .= $output_this_field;
		}
		// Post Excerpt
		if (
            !$explicit_fields && post_type_supports($args['post_type'],'excerpt') && !isset($args['post_excerpt'])
            || ($explicit_fields && in_array('post_excerpt', $custom_fields)) 
        ) {
        	$FieldObj = CCTM::load_object('textarea','fields');
			$post_title_def = array(
				'label' => $args['_label_excerpt'],
				'name' => 'post_excerpt',
				'default_value' => wp_kses(
					CCTM::get_value($_POST,$args['_name_prefix'].'post_excerpt'),array()),
				'extra' => 'cols="80" rows="10"',
				'class' => '',
				'description' => '',
				'validator' => '',
				'output_filter' => '',
				'type' => 'textarea'
			);
			$FieldObj->set_props($post_title_def);
			    $output_this_field = $FieldObj->get_create_field_instance();
			$output['post_excerpt'] = $output_this_field;
			$output['content'] .= $output_this_field;
		}
		
		foreach ( $custom_fields as $cf ) {
			// skip the field if its value is hard-coded
			if (!isset(CCTM::$data['custom_field_defs'][$cf]) || isset($args[$cf])) {
				continue;
			}
			$def = CCTM::$data['custom_field_defs'][$cf];

			if (isset($def['required']) && $def['required'] == 1) {
				$def['label'] = $def['label'] . '*'; // Add asterisk
			}
			// SECURITY OVERRIDES!!! 
			// See https://code.google.com/p/wordpress-custom-content-type-manager/wiki/cctm_post_form
			if ($def['type'] == 'wysiwyg') {
				$def['type'] = 'textarea';
			}
			elseif (in_array($def['type'], array('relation','image','media'))) {
				$output['errors'] .= ' '.$def['type'].' fields not allowed.';
				continue;
			}
			$output_this_field = '';
			if (!$FieldObj = CCTM::load_object($def['type'],'fields')) {
				continue;
			}
			// Repopulate
			if (isset($_POST[ $args['_name_prefix'].$def['name'] ])) {
				$def['default_value'] = wp_kses(
					$_POST[ $args['_name_prefix'].$def['name'] ],array());
			}
					
			if (empty(CCTM::$post_validation_errors)) {	
				$FieldObj->set_props($def);
				$output_this_field = $FieldObj->get_create_field_instance();
			}
			else {
				$current_value = wp_kses(CCTM::get_value($_POST,$args['_name_prefix'].$def['name']),array());
				
				if (isset(CCTM::$post_validation_errors[ $def['name'] ])) {
					$def['error_msg'] = sprintf('<span class="cctm_validation_error">%s</span>', CCTM::$post_validation_errors[ $def['name'] ]);
					if (isset($def['class'])) {
						$def['class'] .= 'cctm_validation_error';
					}
					else {
						$def['class'] = 'cctm_validation_error';
					}
					
				}
				$FieldObj->set_props($def);
				$output_this_field =  $FieldObj->get_edit_field_instance($current_value);
			}
			$output[$cf] = $output_this_field;	
			$output['content'] .= $output_this_field;
		}

		// Add Nonce
		$output['nonce'] = '<input type="hidden" name="_cctm_nonce" value="'.wp_create_nonce('cctm_post_form_nonce').'" />';
        $output['content'] .= $output['nonce'];
        
		// Add Submit
		$output['submit'] = '<input type="submit" value="'.__('Submit', CCTM_TXTDOMAIN).'" />';
        $output['content'] .= $output['submit'];
		
		$formtpl = CCTM::load_tpl(
			array('forms/'.$args['_tpl'].'.tpl'
				, 'forms/_'.$post_type.'.tpl'
				, 'forms/_default.tpl'
			)
		);
		if ($args['_debug']) {
		  $formtpl = '<div style="background-color:orange; padding:10px;"><h3>[cctm_post_form] DEBUG MODE</h3>'
		      .'<h4>Arguments</h4><pre>'.print_r($args,true).'</pre>'
		      .'</div>'
		      .$formtpl;
		}
		return CCTM::parse($formtpl, $output);
		
	}

	/**
	 * Callback function used by the cctm_post_form() function.  This is what gets 
	 * called 
	 *
	 * @param	array $args: parameters from the shortcode and posted data
	 * @return string (printed)	 
	 */
	public static function post_form_handler($args) {
		//return print_r($args,true);
		// Strip out the control stuff (keys begin with underscore)
		$vals = array();
		foreach ($args as $k => $v) {
			if ($k[0] == '_') continue;
			$vals[$k] = $v;
		}
		
		
		// Insert into Database
		$email_only = CCTM::get_value($args, '_email_only');
		if (!$email_only) {
			require_once(CCTM_PATH.'/includes/SP_Post.php');
			$P = new SP_Post();
			CCTM::$post_id = $P->insert($vals);
		}
		
		// Email stuff
		if (isset($args['_email_to']) && !empty($args['_email_to']) 
			&& isset($args['_email_tpl']) && !empty($args['_email_tpl'])) {
		
			$Q = new GetPostsQuery();
			$P = $Q->get_post($args['_email_tpl']);
			//return print_r($P, true);
			$subject = $P['post_title'];
			$message_tpl = wpautop($P['post_content']);
			// If the 'My User' <myuser@email.com> format is used, we have to manipulate the string
			// to keep WP from tripping over itself
			$from = CCTM::get_value($args, '_email_from', get_bloginfo('admin_email'));
			$from = str_replace(array('&#039','&#034;','&quot;','&lt;','&gt;'), array("'",'"','"','<','>'), $from);
			// die(print_r($args,true));
			$subject = CCTM::get_value($args, '_email_subject', $subject);
			$headers = 'From: '.$from . "\r\n";
			$headers .= 'content-type: text/html' . "\r\n";
			
			$message = CCTM::parse($message_tpl, $vals);
			if(!wp_mail($args['_email_to'], $subject, $message, $headers)) {
				return "There was a problem sending the email.";
			}
		}
		
		// Redirect or show a simple message.
		$redirect = CCTM::get_value($args, '_redirect');
		if ($redirect) {
			$url = get_permalink($redirect);
			CCTM::redirect($url, true);
		}
		
		// Else, return message:
		return CCTM::get_value($args, '_msg', "Thanks for submitting the form!");

	}

	//------------------------------------------------------------------------------
	/**
	 * Solves the problem with encodings.  On many servers, the following won't work:
	 *
	 *   print 'ę'; // prints Ä™
	 *
	 * But this function solves it by converting the characters into appropriate html-entities:
	 *
	 *   print charset_decode_utf_8('ę');
	 *
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=88
	 * Solution from Squirrelmail, see http://pa2.php.net/manual/en/function.utf8-decode.php
	 *
	 * @param string $string
	 * @return string
	 */
	public static function charset_decode_utf_8($string) {
		$string = htmlspecialchars($string); // htmlentities will NOT work here.

		/* Only do the slow convert if there are 8-bit characters */
		/* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
		if (!preg_match("/[\200-\237]/", $string) and !preg_match("/[\241-\377]/", $string)) {
			return $string;
		}

		// decode three byte unicode characters
		$string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e", "'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'", $string);

		// decode two byte unicode characters
		$string = preg_replace("/([\300-\337])([\200-\277])/e", "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'", $string);

		return $string;
	}


	//------------------------------------------------------------------------------
	/**
	 * WordPress lacks an "onUpdate" event, so this is a home-rolled way I can run
	 * a specific bit of code when a new version of the plugin is installed. The way
	 * this works is the names of all files inside of the updates/ folder are loaded
	 * into an array, e.g. 0.9.4, 0.9.5.  When the first new page request comes through
	 * WP, the database is in the old state, whereas the code is new, so the database
	 * will say e.g. that the plugin version is 0.1 and the code will say the plugin version
	 * is 0.2.  All the available updates are included and their contents are executed
	 * in order.  This ensures that all update code is run sequentially.
	 *
	 * Any version prior to 0.9.4 is considered "version 0" by this process.
	 *
	 */
	public static function check_for_updates() {

		// If it's not a new install, we check for updates
		if ( version_compare( self::get_stored_version(), self::get_current_version(), '<' ) ) {
			// set the flag
			define('CCTM_UPDATE_MODE', 1);
			cctm_run_tests();
			// Load up available updates in order 
			// Older systems don't have FilesystemIterator
			// $updates = new FilesystemIterator(CCTM_PATH.'/updates', FilesystemIterator::KEY_AS_PATHNAME);
			$updates = scandir(CCTM_PATH.'/updates');
			foreach ($updates as $file) {
                // Skip the gunk
                if ($file === '.' || $file === '..') continue;
                if (is_dir(CCTM_PATH.'/updates/'.$file)) continue;
                if (substr($file, 0, 1) == '.') continue;
                // skip non-php files
                if (pathinfo(CCTM_PATH.'/updates/'.$file, PATHINFO_EXTENSION) != 'php') continue;
                // We don't want to re-run older updates
                $this_update_ver = substr($file, 0, -4);
                if ( version_compare( self::get_stored_version(), $this_update_ver, '<' ) ) {
                        // Run the update by including the file
                        include CCTM_PATH.'/updates/'.$file;
                        // timestamp the update
                        self::$data['cctm_update_timestamp'] = time(); // req's new data structure
                        // store the new version after the update
                        self::$data['cctm_version'] = $this_update_ver; // req's new data structure
                        update_option( self::db_key, self::$data );
                }
			}

			// Clear the cache and such
			unset(CCTM::$data['cache']);
			unset(CCTM::$data['warnings']);
			// Mark the update
			self::$data['cctm_version'] = self::get_current_version();
			update_option(self::db_key, self::$data);
		}

		// If this is empty, then it is a first install, so we timestamp it
		// and prep the data structure
		if (empty(CCTM::$data)) {
			// TODO: run tests
			CCTM::$data['cctm_installation_timestamp'] = time();
			CCTM::$data['cctm_version'] = CCTM::get_current_version();
			CCTM::$data['export_info'] = array(
				'title'   => 'CCTM Site',
				'author'   => get_option('admin_email', ''),
				'url'    => get_option('siteurl', 'http://wpcctm.com/'),
				'description' => __('This site was created in part using the Custom Content Type Manager', CCTM_TXTDOMAIN),
			);
			update_option(CCTM::db_key, CCTM::$data);
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * Create custom post-type menu.  This should only be visible to
	 * admin users (single-sites) or the super_admin users (multi-site).
	 *
	 * See http://codex.wordpress.org/Administration_Menus
	 * http://wordpress.org/support/topic/plugin-custom-content-type-manager-multisite?replies=18#post-2501711
	 */
	public static function create_admin_menu() {
		self::load_file('/config/menus/admin_menu.php');
	}


	//------------------------------------------------------------------------------
	/**
	 * Handles printing of custom fields in the main content blocks.
	 *
	 * @param	array	$raw_args: parameters from the shortcode: name, filter
	 * @param	string	$options (optional)
	 * @return string (printed)
	 */
	public static function custom_field($raw_args=array(), $options = null) {
		$defaults = array(
			'name' => '',
			'filter' => '',
			'post_id' => '',
		);
		$args = shortcode_atts($defaults, $raw_args );
		if (empty($args['name'])) {
			print __('custom_field shortcode requires the "name" parameter.', CCTM_TXTDOMAIN);
		}

		// Append the ':filter' to the name
		if (!empty($args['filter'])) {
			$args['name'] = $args['name'] . ':' . $args['filter'];
		}
		
		// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=120
		// This allows users to specify which post they want to retrieve the data from.
		if (!empty($args['post_id'])) {
			CCTM::$post_id = $args['post_id'];
		}
		
		if (!empty($options)) {
			print_custom_field($args['name'], htmlspecialchars_decode($options));	
		}
		else {
			print_custom_field($args['name']);
		}
	}

    /**
     * Used to customize the tabs shown in the Media Uploader thickbox
     * shown for relation fields.  See the media-upload.php
     *
     */
    public static function customize_upload_tabs($tabs) {
        unset($tabs['type_url']);
        return $tabs;
    }
	/**
	 * Delete a directoroy and its contents.
	 * @param	string $dirPath
	 */
	public static function delete_dir($dirPath) {
	    if (!is_dir($dirPath)) {
	    	return false;
	    }
	    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
	        $dirPath .= '/';
	    }
	    $files = glob($dirPath . '*', GLOB_MARK);
	    foreach ($files as $file) {
	        if (is_dir($file)) {
	            self::delete_dir($file);
	        } 
	        else {
	            unlink($file);
	        }
	    }
	    rmdir($dirPath);
	}

	//------------------------------------------------------------------------------
	/**
	 * This is CCTM's own filter function, the engine behind all available CCTM
	 * Output Filters, but this can also be called statically. 
	 *
	 * @param mixed $value to be filtered, usually a string.
	 * @param string $outputfilter name, e.g. 'to_array'
	 * @param mixed $options (optional) any additional arguments to pass to the filter
	 * @return mixed dependent on output filter
	 */
	public static function filter($value, $outputfilter, $options=null) {
	
		$filter_class = CCTM::filter_prefix.$outputfilter;

		require_once CCTM_PATH.'/includes/CCTM_OutputFilter.php';
		
		if ($OutputFilter = CCTM::load_object($outputfilter,'filters')) {
			return $OutputFilter->filter($value, $options);		
		}
		else {
			return $value;
		}
	}

	/**
	 * This filters the post_name (intercepting the WP sanitize_title event).
	 * This is important for hierarchical post-types because WP incorrectly identifies
	 * the post_name when a hiearchical post-type URL is encountered, e.g. 
	 * 	http://wpcctm.com/movie/lord-of-the-rings/fellowship-of-the-ring/
	 *
	 * WP searches the database via post_name, and it thinks the post_name is:
	 * "lord-of-the-ringsfellowship-of-the-ring".  So we have to grab the last segment
	 * of the URL because only the last segment is stored in the database as the title.
	 *
	 * 
	 *
	 * @param	string	$title the post_name, e.g. "lord-of-the-rings/fellowship-of-the-ring"
	 * @param	string	$raw_title
	 * @param	string 	$context "query" or "save"
	 *
	 * @return string
	 */
	public static function filter_sanitize_title($title, $raw_title, $context) {

		// This isn't always called on the public-side... it gets called in the manager too.
		if ('query' == $context) {
			global $wp_query;
			if (!is_object($wp_query)) {
				return $title;
			}

			$post_type = CCTM::get_value($wp_query->query_vars, 'post_type');
			// Don't mess with foreign post-types.
			// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=425
			if (!is_scalar($post_type)
				|| !isset(CCTM::$data['post_type_defs'][$post_type]['cctm_hierarchical_custom'])) {
				return $title;
			}
			// To avoid problems when this filter is called unexpectedly... e.g. category pages has an array of post_types
			if (empty($post_type) || is_array($post_type)) {
				return $title; // 
			}
			//checking cctm_hierarchical_custom is not necessary because BOTH boxes must be checked.
			// Get the last URL segment.  E.g. given house/room/chair, this would return 'chair'
			if (isset(CCTM::$data['post_type_defs'][$post_type]) && CCTM::$data['post_type_defs'][$post_type]['hierarchical'] ) {
				$segments = explode('/',$title);
				$title = array_pop($segments);
			}
		}
		
		return $title;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Adds formatting to a string to make an "error" message.
	 *
	 * @param mixed $msg localized error message, or an array of messages
	 * @param string $title optional
	 * @return string
	 */
	public static function format_error_msg($msg,$title='') {
		if ($title) {
			$title = '<h3>'.$title.'</h3>';
		}
		if (is_array($msg)) {
			$tmp = '';
			foreach($msg as $m) {
				$tmp .= '<li>'.$m.'</li>';
			}
			$msg = '<ul style="margin-left:30px">'.$tmp.'</ul>';
		}
		return sprintf('<div class="error">%s<p>%s</p></div>', $title, $msg);
	}


	//------------------------------------------------------------------------------
	/**
	 * Adds formatting to a string to make an "updated" message.
	 *
	 * @param string $msg localized message
	 * @return string
	 */
	public static function format_msg($msg) {
		return sprintf('<div class="updated"><p>%s</p></div>', $msg);
	}
	
	//------------------------------------------------------------------------------
	/**
	 * This formats any errors registered in the class $errors array. The errors
	 * take this format: self::$errors['field_name'] = 'Description of error';
	 *
	 * @return string (empty string if no errors)
	 */
	public static function format_errors() {
		$error_str = '';
		if ( empty ( self::$errors ) ) {
			return '';
		}

		foreach ( self::$errors as $e ) {
			$error_str .= '<li>'.$e.'</li>
			';
		}

		return sprintf('<div class="error">
			<p><strong>%1$s</strong></p>
			<ul style="margin-left:30px">
				%2$s
			</ul>
			</div>'
			, __('Please correct the following errors:', CCTM_TXTDOMAIN)
			, $error_str
		);
	}

	//------------------------------------------------------------------------------
	/**
	 * Returns an array of active post_types (i.e. ones that will a have their fields
	 * standardized.
	 *
	 * @return array
	 */
	public static function get_active_post_types() {
		$active_post_types = array();
		if ( isset(self::$data['post_type_defs']) && is_array(self::$data['post_type_defs'])) {
			foreach (self::$data['post_type_defs'] as $post_type => $def) {
				if ( isset($def['is_active']) && $def['is_active'] == 1 ) {
					$active_post_types[] = $post_type;
				}

			}
		}

		return $active_post_types;
	}


	//------------------------------------------------------------------------------
	/**
	 * Custom manipulation of the WHERE clause used by the wp_get_archives() function.
	 * WP deliberately omits custom post types from archive results.
	 *
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=13
	 *
	 * @param string $where
	 * @param unknown $r
	 * @return string
	 */
	public static function get_archives_where_filter( $where , $r ) {
		// Get only public, custom post types
		$args = array( 'public' => true, '_builtin' => false );
		$public_post_types = get_post_types( $args );
		// Only posts get archives... not pages.
		$search_me_post_types = array('post');

		// check which have 'has_archive' enabled.
		if (isset(self::$data['post_type_defs']) && is_array(self::$data['post_type_defs'])) {
			foreach (self::$data['post_type_defs'] as $post_type => $def) {
				if ( isset($def['has_archive']) && $def['has_archive'] && in_array($post_type, $public_post_types)) {
					$search_me_post_types[] = $post_type;
				}
			}
		}
		$post_types = "'" . implode( "' , '" , $search_me_post_types ) . "'";

		return str_replace( "post_type = 'post'" , "post_type IN ( $post_types )" , $where );
	}

	//------------------------------------------------------------------------------
	/**
	 * Gets an array of full pathnames/filenames for all helper classes (validators,
	 * Output Filters, or Custom Fields).
	 * This searches the built-in location AND the add-on location inside
	 * wp-content/uploads. If there are duplicate filenames, the one inside the
	 * 3rd party directory will take precedence: this allows developers to override
	 * the built-in classes.
	 *
	 * 3rd party Custom field classes are special: they use a '.class.php' suffix and their 
	 * files can reside in a subdirectory, e.g. fields/myfield/myfield.class.php
	 * This is because custom fields may have ancillary php files that should not be 
	 * counted as viable helper classes. 
	 *
	 * @param string $type validators|filters|fields
	 * @return array Associative array: array('shortname' => '/full/path/to/shortname.php')
	 */
	public static function get_available_helper_classes($type) {
		
		// return from cache, if available
		if(isset(self::$data['cache']['helper_classes'][$type])) {
			return self::$data['cache']['helper_classes'][$type];
		}
		
		// Ye olde output
		$files = array();
		
		// Scan default directory
		$dir = CCTM_PATH .'/'.$type;
		$rawfiles = scandir($dir);
		foreach ($rawfiles as $f) {
			if ( !preg_match('/^\./', $f) && preg_match('/\.php$/', $f) ) {
				$shortname = basename($f);
				$shortname = preg_replace('/\.php$/', '', $shortname);
				$files[$shortname] = $dir.'/'.$f;
			}
		}

		// Scan 3rd party directory
		$upload_dir = wp_upload_dir();
		if (isset($upload_dir['error']) && !empty($upload_dir['error'])) {
			self::register_warning( __('WordPress issued the following error: ', CCTM_TXTDOMAIN) .$upload_dir['error']);
		}
		else {
			$dir = $upload_dir['basedir'] .'/'.CCTM::base_storage_dir . '/'.$type;
			if (is_dir($dir)) {
				$rawfiles = scandir($dir);
				foreach ($rawfiles as $f) {
					if (preg_match('/^\./', $f)) {
						continue; // skip the '.' and '..' dirs
					}
					// Check the main dir
					if (preg_match('/\.php$/', $f) ) {
						$shortname = basename($f);
						$shortname = preg_replace('/\.php$/', '', $shortname);
						$files[$shortname] = $dir.'/'.$f;
					}
					// check subdirectories
					elseif (is_dir($dir.'/'.$f)) {
						$morerawfiles = scandir($dir.'/'.$f);
						foreach ($morerawfiles as $f2) {
							if ( !preg_match('/^\./', $f2) && preg_match('/\.class\.php$/', $f2) ) {
								$shortname = basename($f2);
								$shortname = preg_replace('/\.class\.php$/', '', $shortname);
								$files[$shortname] = $dir.'/'.$subdir.'/'.$f2;
							}
						}
					}
				}
			}
		}
		
		// write to cache
		self::$data['cache']['helper_classes'][$type] = $files;
		update_option(self::db_key, self::$data);
		
		return $files;

	}

	//------------------------------------------------------------------------------
	/**
	 * Gets the plugin version from this class.
	 *
	 * @return string
	 */
	public static function get_current_version() {
		return self::version .'-'. self::version_meta;
	}


	//------------------------------------------------------------------------------
	/**
	 * Interface with the model: retrieve the custom field definitions, sorted.
	 *
	 * @return array
	 */
	public static function get_custom_field_defs() {
		if ( isset(self::$data['custom_field_defs']) ) {
//		      print '<pre>'.print_r(self::$data['custom_field_defs'],true).'</pre>';
			// sort them
			$defs = self::$data['custom_field_defs'];
			usort($defs, CCTM::sort_custom_fields('name', 'strnatcasecmp'));

			foreach ($defs as $i => $d ) {
				$field_name = $d['name'];
				$defs[$field_name] = $d; // re-establish the key version.
				unset($defs[$i]); // kill the integer version
			}

			return $defs;
		}
		else {
			return array();
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * Returns a path with trailing slash.
	 *
	 * @return string
	 */
	public static function get_custom_icons_src_dir() {
		self::$custom_field_icons_dir = CCTM_URL.'/images/custom-fields/';
		return self::$custom_field_icons_dir;
	}


	//------------------------------------------------------------------------------
	/**
	 * Get the flash message (i.e. a message that persists for the current user only
	 * for the next page view). See "Flashdata" here:
	 * http://codeigniter.com/user_guide/libraries/sessions.html
	 *
	 * @return message
	 */
	public static function get_flash() {
		$output = '';
		$key = self::get_user_identifier();
		if (isset(self::$data['flash'][$key])) {
			$output = self::$data['flash'][$key];
			unset( self::$data['flash'][$key] );
			update_option(self::db_key, self::$data);
			return html_entity_decode($output);
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * Used to identify the current user for flash messages and screen locks
	 *
	 * @return integer
	 */
	public static function get_user_identifier() {
		global $current_user;
		if (!isset($current_user->ID) || empty($current_user->ID)) {
			return 0;
		}
		else {
			return $current_user->ID;
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * return all post-type definitions
	 *
	 * @return array
	 */
	public static function get_post_type_defs() {
		if ( isset(self::$data['post_type_defs']) && is_array(self::$data['post_type_defs'])) {
			return self::$data['post_type_defs'];
		}
		else {
			return array();
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Similar to WordPress' get_post_types(), this function retrieves all registered
	 * post-types AND any inactive CCTM post-types that have a CCTM definition.
	 * This will skip any post-types for which the CCTM only has custom field associations
	 * but which are no longer registered (i.e. is_foreign=1, but now abandoned)
	 *
	 * @return 	array	of post_types
	 */
	public static function get_post_types() {
		$registered = get_post_types();
		$cctm_types = array();

		// this has the side-effect of sorting the post-types
		if ( isset(CCTM::$data['post_type_defs']) && !empty(CCTM::$data['post_type_defs']) ) {
			$cctm_types =  array_keys(CCTM::$data['post_type_defs']);
		}
		$all_types = array_merge($registered , $cctm_types);
		$all_types = array_unique($all_types); // make unique
		
		$filtered = array();
		
		foreach ($all_types as $pt) {
			if (in_array($pt, self::$ignored_post_types)) {
				continue;
			}
			// Abandonded foreign
			if (!isset(CCTM::$data['post_type_defs'][$pt]['post_type']) 
			&& !in_array($pt, $registered)) {
				continue;
			}
			// Optionall skip foreigns altogether
			//if (!isset(CCTM::$data['post_type_defs'][$pt]['post_type'])
			//	&& !self::get_setting('show_foreign_post_types')) {
			//	continue;
			//}
			$filtered[] = $pt;
		}

		return $filtered;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Gets the plugin version (used to check if updates are available). This checks
	 * the database to see what the database thinks is the current version. Right
	 * after an update, the database will think the version is older than what
	 * the CCTM class will show as the current version. We use this to trigger 
	 * modifications of the CCTM data structure and/or database options.
	 *
	 * @return string
	 */
	public static function get_stored_version() {
		if (isset(self::$data['cctm_version'])) {
			return self::$data['cctm_version'];
		}
		else {
			return '0';
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * Read the value of a setting.  Will use default value if the setting is not
	 * yet defined (e.g. when the user hasn't updated their settings.
	 *
	 * @param string $setting name (see class var $default_settings)
	 * @return mixed
	 */
	public static function get_setting($setting) {

		if (empty($setting)) {
			return '';
		}
		if (isset(self::$data['settings']) && is_array(self::$data['settings'])) {
			if (isset(self::$data['settings'][$setting])) {
				return self::$data['settings'][$setting];
			}
			elseif (isset(self::$default_settings[$setting])) {
				return self::$default_settings[$setting];
			}
			else {
				return ''; // setting not found :(
			}
		}
		elseif (isset(self::$default_settings[$setting])) {
			return self::$default_settings[$setting];
		}
		else {
			return '';
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * This will get thumbnail info and append it to the record, creating cached 
	 * images on the fly if possible.  The following keys are added to the array:
	 *
	 *		thumbnail_url
	 *
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=256
	 *
	 * What we need to get a thumbnail:
	 *	guid, post_type, ID, post_mime_type
	 * @param	integer	$id of the post for which we want the thumbnail
	 * @return	string	url of the thumbnail
	 */
	public static function get_thumbnail($id) {

		// Default output
		$thumbnail_url = CCTM_URL .'/images/custom-fields/default.png';

		if (empty($id) || $id == 0) {
			return $thumbnail_url;
		}
		
		$post = get_post($id, ARRAY_A);
		$guid = $post['guid'];
		$post_type = $post['post_type'];
		$post_mime_type = $post['post_mime_type'];
		$thumbnail_url = $post['guid'];
		
		// Some translated labels and stuff
		$r['preview'] = __('Preview', CCTM_TXTDOMAIN);
		$r['remove'] = __('Remove', CCTM_TXTDOMAIN);
		$r['cctm_url'] = CCTM_URL;
		


		// Special handling for media attachments (i.e. photos) and for 
		// custom post-types where the custom icon has been set.
		if ($post_type == 'attachment' && preg_match('/^image/',$post_mime_type) && self::get_setting('cache_thumbnail_images')) {
			$thumbnail_url = self::_get_create_thumbnail($post);
		}
		// Try to display the featured thumbnail if possible
		elseif ($thumbnail_id = get_post_thumbnail_id($id)) {
			list($src, $w, $h) = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail', true, array('alt'=>__('Preview', CCTM_TXTDOMAIN)));
			$thumbnail_url = $src;            
        }
		elseif (isset(CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon']) 
				&& CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon'] == 0) {
			$baseimg = basename(CCTM::$data['post_type_defs'][$post_type]['menu_icon']);
			$thumbnail_url = CCTM_URL . '/images/icons/32x32/'. $baseimg;	
		}
		elseif ($post_type == 'post') {
			$thumbnail_url = CCTM_URL . '/images/wp-post.png';
		}
		elseif ($post_type == 'page') {
			$thumbnail_url = CCTM_URL . '/images/wp-page.png';	
		}
		// Other built-in WP types: we go for the default icon.
		else {
			list($src, $w, $h) = wp_get_attachment_image_src( $id, 'thumbnail', true, array('alt'=>__('Preview', CCTM_TXTDOMAIN)));
			$thumbnail_url = $src;
		}

		return $thumbnail_url;	
	}

	//------------------------------------------------------------------------------
	/**
	 * Designed to safely retrieve scalar elements out of a hash. Don't use this
	 * if you have a more deeply nested object (e.g. an array of arrays).
	 *
	 * @param array   $hash    an associative array, e.g. array('animal' => 'Cat');
	 * @param string  $key     the key to search for in that array, e.g. 'animal'
	 * @param mixed   $default (optional) : value to return if the value is not set. Default=''
	 * @return mixed
	 */
	public static function get_value($hash, $key, $default='') {
		if ( !isset($hash[$key]) ) {
			return $default;
		}
		else {
			if ( is_array($hash[$key]) ) {
				return $hash[$key];
			}
			// Warning: stripslashes was added to avoid some weird behavior... but beware, it causes others
			else {
				return esc_html(stripslashes($hash[$key]));
			}
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * !TODO: see http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=170
	 *
	 * @param unknown $stuff
	 * @return string
	 */
	public static function highlight_cctm_compatible_themes($stuff) {
		$stuff[] = 'CCTM compatible!';
		return $stuff;
	}

	//------------------------------------------------------------------------------
	/**
	 * Each custom field can optionally do stuff during the admin_init event -- this
	 * was designed so custom fields could include their own JS & CSS, but it could
	 * be used for other purposes I suppose, e.g. registering other actions/filters.
	 *
	 * Custom field classes will be included and initialized only in the following
	 * two cases:
	 *  1. when creating/editing a post that uses one of these fields
	 *  2. when creating/editing a field definition of the type indicated.
	 * E.g.
	 *  post-new.php
	 *  post-new.php?post_type=page
	 * 	post.php?post=807
	 *  admin.php?page=cctm_fields&a=create_custom_field
	 *  admin.php?page=cctm_fields&a=edit_custom_field
	 */
	public static function initialize_custom_fields() {

		// Look around/read variables to get our bearings
		$page = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1);
		$fieldtype = self::get_value($_GET, 'type');
		$fieldname = self::get_value($_GET, 'field');
		$action = self::get_value($_GET, 'a');
		$post_type = 'post'; // default
		// Bail if we're not on the relevant pages
		if (!in_array($page,array('post.php','post-new.php','admin.php'))) {
			return;
		}
		
		if ($page == 'post-new.php') {
			$post_type = self::get_value($_GET, 'post_type', 'post'); // post_type is only set for NEW posts
		}
		else { // ( $page == 'post.php') {
			$post_id = self::get_value($_POST, 'post_ID');
			// TODO: wouldn't you think the post_type was already defined somewhere?
			if (empty($post_id)) {
				$post_id = self::get_value($_GET, 'post');		
			}
			
			$post = get_post($post_id);
			if (!empty($post)) {
				$post_type = $post->post_type;
			}
			
		}
		
		// Here's where we will load up all the field-types that are active on this particular post or page.
		$field_types = array();
		
		// Create/edit posts
		if ( ($page == 'post.php') || ($page == 'post-new.php') ) {
			if (isset(self::$data['post_type_defs'][$post_type]['is_active'])) {
				$custom_fields = self::get_value(self::$data['post_type_defs'][$post_type], 'custom_fields', array() );
				
				// We gotta lookup the fieldtype by the name
				foreach ($custom_fields as $cf) {
					if (!isset(self::$data['custom_field_defs'][$cf])) {
						// unset this? 
						continue; // we shouldn't get here, but just in case...
					}
					// Get an array of field-types for this 
					$fieldtype = self::get_value(self::$data['custom_field_defs'][$cf], 'type');
					if (!empty($fieldtype)) {
						$field_types[$fieldtype][] = $cf;
					}
				}
			}
		}
		// Create custom field definitions
		elseif ( $page == 'admin.php' && $action == 'create_custom_field') {
			$field_types[$fieldtype] = array();
		}
		// Edit custom field definitions (the name is specified, not the type)
		elseif ( $page == 'admin.php' && $action == 'edit_custom_field' && isset(self::$data['custom_field_defs'][$fieldname])) {
			$fieldtype = self::get_value(self::$data['custom_field_defs'][$fieldname], 'type');
			$field_types[$fieldtype][] = $fieldname;
		}
		elseif ($page == 'admin.php' && $action =='duplicate_custom_field') {
            $fieldtype = CCTM::get_value($_GET,'type');
			$field_types[$fieldtype] = array(); 
		}

		// We only get here if we survived the gauntlet above
		foreach ($field_types as $ft => $fieldlist) {
			if ($FieldObj = CCTM::load_object($ft, 'fields')) {			
				$FieldObj->admin_init($fieldlist);
			}
		}

		if (!empty(CCTM::$errors)) {
			self::print_notices();
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * Used when generating checkboxes in forms. Any non-empty non-zero incoming value will cause
	 * the function to return checked="checked"
	 *
	 * Simple usage uses just the first parameter: if the value is not empty or 0,
	 * the box will be checked.
	 *
	 * Advanced usage was built for checking a list of options in an array (see
	 * register_post_type's "supports" array).
	 *
	 * @param mixed   normally a string, but if an array, the 2nd param must be set
	 * @param string  $find_in_array (optional) value to look for inside the $input array.
	 * @return string either '' or 'checked="checked"'
	 */
	public static function is_checked($input, $find_in_array='') {
		if ( is_array($input) ) {
			if ( in_array($find_in_array, $input) ) {
				return 'checked="checked"';
			}
			else {
				return '';
			}
		}
		else {
			if (!empty($input) && $input!=0) {
				return 'checked="checked"';
			}
		}
		return ''; // default
	}


	//------------------------------------------------------------------------------
	/**
	 * Like the is_selected function, but for radio inputs.
	 * If $option_value == $field_value, then this returns 'selected="selected"'
	 *
	 * @param string  $option_value:  the value of the <option> being tested
	 * @param string  $current_value: the current value of the field
	 * @return string
	 */
	public static function is_radio_selected($option_value, $current_value) {
		if ( $option_value == $current_value ) {
			return 'checked="checked"';
		}
		return '';
	}


	//------------------------------------------------------------------------------
	/**
	 * If $option_value == $field_value, then this returns 'selected="selected"'
	 *
	 * @param string  $option_value:  the value of the <option> being tested
	 * @param string  $current_value: the current value of the field
	 * @return string
	 */
	public static function is_selected($option_value, $current_value) {
		if ( $option_value == $current_value ) {
			return 'selected="selected"';
		}
		return '';
	}


	//------------------------------------------------------------------------------
	/**
	 * This translates a local URL to a path on this server so that we can use 
	 * file_exists() to check whether or not it exists.  
	 *
	 * Alternatives for checking for files by their URL, such as:
	 *  if (!@fclose(@fopen($src, 'r'))) {
	 * 		$src = CCTM_URL.'/images/custom-fields/default.png';
	 * 	}
	 *
	 * caused segfaults in some server configurations (see issue 60):
	 * http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=60
	 * So in order to check whether an image path is broken or not, we translate the
	 * $src URL into a local path so we can use humble file_exists() instead.
	 *
	 * This must also be able to handle when WP is installed in a sub directory.
	 *
	 *     or 'http://mysite.com/some/img.jpg'
	 *
	 * @param string  $src a URL path to an image ON THIS SERVER, e.g. '/wp-content/uploads/img.jpg'
	 * @return boolean true if the img is valid, false if the img link is broken
	 */
	public static function is_valid_img($src) {

		$info = parse_url($src);

		// Bail on malformed URLs
		if (!$info) {
			return false;
		}
		// Ensure places in the array
		if(!isset($info['port'])) { $info['port'] = ''; }
		if(!isset($info['scheme'])) { $info['scheme'] = ''; }
		if(!isset($info['host'])) { $info['host'] = ''; }
		
		// Is this image hosted on another server? (currently that's not allowed)
		if ( isset($info['scheme']) ) {
			$this_site_info = parse_url( get_site_url() );
			// Ensure places in the array
			if(!isset($this_site_info['port'])) { $this_site_info['port'] = ''; }
			if(!isset($this_site_info['scheme'])) { $this_site_info['scheme'] = ''; }
			if(!isset($this_site_info['host'])) { $this_site_info['host'] = ''; }

			if ( $this_site_info['scheme'] != $info['scheme']
				|| $this_site_info['host'] != $info['host']
				|| $this_site_info['port'] != $info['port']) {

				return false;
			}
		}

		// Gives us something like "/home/user/public_html/blog"
		$ABSPATH_no_trailing_slash = preg_replace('#/$#', '', ABSPATH);

		// This will tell us whether WP is installed in a subdirectory
		$wp_info = parse_url(site_url());

		// This works when WP is installed @ the root of the site
		if ( !isset($wp_info['path']) ) {
			$path = $ABSPATH_no_trailing_slash . $info['path'];
		}
		// But if WP is installed in a sub dir...
		else {
			$path_to_site_root = preg_replace('#'.preg_quote($wp_info['path']).'$#'
				, ''
				, $ABSPATH_no_trailing_slash);
			$path = $path_to_site_root . $info['path'];
		}

		if ( file_exists($path) ) {
			return true;
		}
		else {
			return false;
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * Load CCTM data from database.
	 */
	public static function load_data() {
		self::$data = get_option( CCTM::db_key, array() );
	}

	//------------------------------------------------------------------------------
	/**
	 * When given a PHP file name relative to the CCTM_PATH, e.g. '/config/image_search_parameters.php',
	 * this function will include (or require) that file. However, if the same file exists
	 * in the same location relative to the wp-content/uploads/cctm directory, THAT version of 
	 * the file will be used. E.g. calling load_file('test.php') will include 
	 * wp-content/uploads/cctm/test.php (if it exists); if the file doesn't exist in the uploads
	 * directory, then we'll look for the file inside the CCTM_PATH, e.g.
	 * wp-content/plugins/custom-content-type-manager/test.php 
	 *
	 * The purpose of this is to let users use their own version of files by placing them in a 
	 * location *outside* of this plugin's directory so that the user-created files will be safe
	 * from any overriting or deleting that may occur if the plugin is updated.
	 *
	 * Developers of 3rd party components can supply $additional_paths if they wish to load files
	 * in their components: if the $additional_path is supplied, this directory will be searched for tpl in question.
	 *
	 * To prevent directory transversing, file names may not contain '..'!
	 *
	 * @param	array|string	$files: filename relative to the path, e.g. '/config/x.php'. Should begin with "/"
	 * @param	array|string	(optional) $additional_paths: this adds one more paths to the default locations. OMIT trailing /, e.g. called via dirname(__FILE__)
	 * @param	string			$load_type (optional) include|include_once|require|require_once -- default is 'include'
	 * @param	mixed	file name used on success, false on fail.
	 */
	public static function load_file($files, $additional_paths=array(), $load_type='include') {

		if (!is_array($files)){
			$files = array($files);
		}

		if (!is_array($additional_paths)){
			$additional_paths = array($additional_paths);
		}
		
		// Populate the list of directories we will search in order. 
		$upload_dir = wp_upload_dir();
		$paths = array();
		$paths[] = $upload_dir['basedir'] .'/'.CCTM::base_storage_dir;
		$paths[] = CCTM_PATH;
		$paths = array_merge($paths, $additional_paths);

		// pull a file off the stack, then look for it
		$file = array_shift($files);
		
		if (preg_match('/\.\./', $file)) {
			die( sprintf(__('Invaid file name! %s  No directory traversing allowed!', CCTM_TXTDOMAIN), '<em>'.htmlspecialchars($file).'</em>'));
		}
		
		if (!preg_match('/\.php$/', $file)) {
			die( sprintf(__('Invaid file name! %s  Name must end with .php!', CCTM_TXTDOMAIN), '<em>'.htmlspecialchars($file).'</em>'));
		}		
		
		// Look through the directories in order.
		foreach ($paths as $dir) {
			if (file_exists($dir.$file)) {
				// Variable functions didn't seem to work here.
				switch ($load_type) {
					case 'include':
						include $dir.$file;
						break;
					case 'include_once':
						include_once $dir.$file;
						break;
					case 'require':
						require $dir.$file;
						break;
					case 'require_once':
						require_once $dir.$file;
						break;
				}
				
				return $dir.$file;
			}
		}
		
		// Try again with the remaining files... or fail.
		if (!empty($files)) {
			return self::load_file($files, $additional_paths, $load_type);
		}
		else {
			return false;
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Load up an object: this handles finding and including the object file, instantiating
	 * it, and returning the object.
	 *
	 * @param	string $shortname
	 * @param	string $type: fields|filters|validators
	 * @return	mixed obect if found, false if not
	 */
	public static function load_object($shortname, $type) {
//        print '<pre>'; print_r( self::$data['cache']); print '</pre>';
		$path = '';	
		$object_classname = '';
		switch ($type) {
			case 'fields':
				$object_classname = self::field_prefix . $shortname;
				break;
			case 'filters':
				$object_classname = self::filter_prefix . $shortname;
				break;
			case 'validators':
				$object_classname = self::validator_prefix . $shortname;
				break;
		}
		

		// Already included?
		if (class_exists($object_classname)) {
			return new $object_classname();
		}

		// The path to the file is cached?  See get_available_helper_classes()
		if(isset(self::$data['cache']['helper_classes'][$type][$shortname])) {
			$path = self::$data['cache']['helper_classes'][$type][$shortname];
		}
		// populate the cache and try again
		else {
			$classes = self::get_available_helper_classes($type);
			if(isset($classes[$shortname])) {
				$path = $classes[$shortname];
			}
			else {
				return false;
			}
		}
		
		switch ($type) {
			case 'fields':				
				require_once(CCTM_PATH.'/includes/CCTM_FormElement.php');
				break;
			case 'filters':
				require_once(CCTM_PATH.'/includes/CCTM_OutputFilter.php');
				break;
			case 'validators':
				require_once(CCTM_PATH.'/includes/CCTM_Validator.php');
				break;
		}
		// Include the file whose path was cached
		require_once($path);
		
		if (class_exists($object_classname)) {
			return new $object_classname();
		}
		else {
			self::$errors['incorrect_classname'] = sprintf( __('Incorrect class name in %s. Expected class name: %s', CCTM_TXTDOMAIN)
					, "<strong>$path</strong>"
					, "<strong>$object_classname</strong>"
				);
			return false; // bogus file that did not declare the correct class
		}		

	}
	
	//------------------------------------------------------------------------------
	/**
	 * Similar to the load_view function, this retrieves a tpl.  It allows users to
	 * override the built-in tpls (stored in the plugin's directory) with tpls stored
	 * in the wp uploads directory.
	 *
	 * If you supply an array of arguments to $name, the first tpl (in the array[0] position)
	 * will be looked for first in the customized directories, then in the built-ins.  If nothing
	 * is found, the array is shifted and the next item in the array is looked for, first in the 
	 * customized locations, then in the built-in locations.  By shifting the array, you can specify
	 * a hierarchy of "fallbacks" to look for with any tpl.
	 *
	 * Developers of 3rd party components can supply additional paths $path if they wish to use tpls
	 * in their components: if the $additional_path is supplied, this directory will be searched for tpl in question.
	 *
	 * To prevent directory transversing, tpl names may not contain '..'!
	 *
	 * @param	array|string	$name: single name or array of tpl names, each relative to the path, e.g. 'fields/date.tpl'. The first one in the list found will be used.
	 * @param	array|string	(optional) $additional_paths: this adds one more path to the default locations. OMIT trailing /, e.g. called via dirname(__FILE__)
	 * @return	string	the file contents (not parsed) OR a boolean false if nothing was found.
	 */
	public static function load_tpl($tpls, $additional_paths=array()) {

		if (!is_array($tpls)){
			$tpls = array($tpls);
		}
		if (!is_array($additional_paths)){
			$additional_paths = array($additional_paths);
		}
		
		// Populate the list of directories we will search in order. 
		$upload_dir = wp_upload_dir();
		$paths = array();
		$paths[] = $upload_dir['basedir'] .'/'.CCTM::base_storage_dir.'/tpls';
		$paths[] = CCTM_PATH.'/tpls';
		$paths = array_merge($paths, $additional_paths);

		// Pull the tpl off the stack
		$tpl = array_shift($tpls);

		if (preg_match('/\.\./', $tpl)) {
			die( sprintf(__('Invaid tpl name! %s  No directory traversing allowed!', CCTM_TXTDOMAIN), '<em>'.htmlspecialchars($tpl).'</em>'));
		}
		
		if (!preg_match('/\.tpl$/', $tpl)) {
			die( sprintf(__('Invaid tpl name! %s  Name must end with .tpl!', CCTM_TXTDOMAIN), '<em>'.htmlspecialchars($tpl).'</em>'));
		}		
		
		// Look through the directories in order.
		foreach ($paths as $dir) {
			if (file_exists($dir.'/'.$tpl)) { 
				return file_get_contents($dir.'/'.$tpl);
			}
		}

		// Try again with the remaining tpls... or fail.
		if (!empty($tpls)) {
			return self::load_tpl($tpls, $additional_paths);
		}
		else {
			return false;
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Load up a PHP file into a string via an include statement. MVC type usage here.
	 *
	 * @param string  $filename (relative to the views/ directory)
	 * @param array   $data (optional) associative array of data
	 * @param string  $path (optional) pathname. Can be overridden for 3rd party fields
	 * @return string the parsed contents of that file
	 */
	public static function load_view($filename, $data=array(), $path=null) {
		if (empty($path)) {
			$path = CCTM_PATH . '/views/';
		}
		if (is_file($path.$filename)) {
			ob_start();
			include $path.$filename;
			return ob_get_clean();
		}
		die('View file does not exist: ' .$path.$filename);
	}

    //------------------------------------------------------------------------------
    /**
     * Simple logging function
     * @param string $msg to be logged
     * 
     */
    public static function log($msg, $file='unknown', $line='?') {
        if (defined('CCTM_DEBUG')) {
            if (CCTM_DEBUG === true) {
                error_log($msg);
            }
            else {	
                $myFile = CCTM_DEBUG;
                $fh = fopen($myFile, 'a') or die("CCTM Failure: Can't open file for appending: ".CCTM_DEBUG);
                fwrite($fh, sprintf("[CCTM %s:%s] %s\n", $msg,$file,$line));
                fclose($fh);
            }
        }    
    }

	//------------------------------------------------------------------------------
	/**
	 * Since WP doesn't seem to support sorting of custom post types, we have to
	 * forcibly tell it to sort by the menu order. Perhaps this should kick in
	 * only if a post_type's def has the "Attributes" box checked?
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=142
	 *
	 * @param string  $orderBy
	 * @return string
	 */
	public static function order_posts($orderBy) {
		$post_type = self::get_value($_GET, 'post_type');
		if (empty($post_type)) {
			return $orderBy;
		}
		
		if (isset(self::$data['post_type_defs'][$post_type]['custom_orderby']) 
			&& !empty(self::$data['post_type_defs'][$post_type]['custom_orderby'])) {
			// die(print_r(self::$data['post_type_defs'][$post_type], true));
			global $wpdb;
			$order = self::get_value(self::$data['post_type_defs'][$post_type], 'custom_order', 'ASC');
			$column = self::$data['post_type_defs'][$post_type]['custom_orderby'];
			if (in_array($column, self::$reserved_field_names)) {
				$orderBy = "{$wpdb->posts}.$column $order";
			}
			// Sort on custom column (would require that custom columns are enabled)
			else {
				$orderBy = "{$wpdb->postmeta}.meta_value $order";			
			}
		}

		return $orderBy;
	}


	//------------------------------------------------------------------------------
	/**
	 * This is the grand poobah of functions for the admin pages: it routes requests
	 * to specific functions.
	 * This is the function called when someone clicks on the settings page.
	 * The job of a controller is to process requests and route them.
	 *
	 */
	public static function page_main_controller() {

		// TODO: this should be specific to the request
		if (!current_user_can('manage_options')) {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		// Grab any possible parameters that might get passed around in the URL
		$action  = self::get_value($_GET, 'a');
		$post_type = self::get_value($_GET, 'pt');
		$file   = self::get_value($_GET, 'file');
		$field_type = self::get_value($_GET, 'type');
		$field_name = self::get_value($_GET, 'field');


		// Default Actions for each main menu item (see create_admin_menu)
		if (empty($action)) {
			$page = self::get_value($_GET, 'page', 'cctm');
			switch ($page) {
			case 'cctm': // main: custom content types
				$action = 'list_post_types';
				break;
			case 'cctm_fields': // custom-fields
				$action = 'list_custom_fields';
				break;
			case 'cctm_metaboxes': // custom-metaboxes
				$action = 'list_metaboxes';
				break;
			case 'cctm_settings': // settings
				$action = 'settings';
				break;
			case 'cctm_themes': // themes
				$action = 'themes';
				break;
			case 'cctm_tools': // tools
				$action = 'tools';
				break;
			case 'cctm_info': // info
				$action = 'info';
				break;
			case 'cctm_cache':
				$action = 'clear_cache';
				break;
			}
		}

		// Validation on the controller name to prevent mischief:
		if ( preg_match('/[^a-z_\-]/i', $action) ) {
			include CCTM_PATH.'/controllers/404.php';
			return;
		}

		$requested_page = CCTM_PATH.'/controllers/'.$action.'.php';

		if (file_exists($requested_page)) {
			include $requested_page;
		}
		else {
			include CCTM_PATH.'/controllers/404.php';
		}
		return;
	}


	//------------------------------------------------------------------------------
	/**
	 * Our parsing function for basic templating, based on the MODX placeholders
	 * and output filters
	 *
	 * @param string  $tpl: a string containing [+placeholders+]
	 * @param array   $hash: an associative array('key' => 'value') corresponding to the keys of the hash will be replaced
	 * @param boolean $preserve_unused_placeholders (optional) if true, will not remove unused [+placeholders+]
	 * @return string parsed text
	 */
	public static function parse($tpl, $hash, $preserve_unused_placeholders=false) {
		if (is_array($hash)) {
			// Get all placeholders in this tpl
			$all_placeholders = array_keys($hash);
			$hash['help'] = '<ul>';
			foreach ($all_placeholders as $p) {
				$hash['help'] .= "<li>&#91;+$p+&#93;</li>";
			}
			$hash['help'] .= '</ul>';
	
			// Simple Placeholders
			foreach ($hash as $key => $value) {
				if (is_scalar($value)) {
					$tpl = str_replace('[+'.$key.'+]', $value, $tpl);
				}
			}
			// ADVANCED PLACEHOLDERS, e.g. [+my_field:output_filter==opt1||opt2:output_filter2+]
			// Check for in-line output filters, e.g. some_id:to_image_tag or post_id:get_post:guid
			$pattern = preg_quote('[+').'(.*)'.preg_quote('+]');
			$placeholders = array();
			preg_match_all('/'.$pattern.'/U', $tpl,$placeholders);
			foreach($placeholders[1] as $complex_ph) {
				//die(print_r($placeholders[1],true));
				$components = explode(':', $complex_ph);
				// First placeholder would be what the simple placeholder would use
				// and the first value comes from the original $hash
				$first = array_shift($components);
				$value = '';
				if (isset($hash[$first])) {
					$value = $hash[$first]; 
				}
				elseif($preserve_unused_placeholders) {
					continue;
				}
				
				// "Components" are the filter==opt chunks
				foreach($components as $comp) {
					// does this value exist?
					$filter_and_opts = explode('==',$comp);
					// $filter_and_opts[0] = the filter name
					// $filter_and_opts[1] = comma-sep options (if any)
					$options = null;
					if (isset($filter_and_opts[1])) {
						// if you used alternate glyphs for nested tags, here's where 
						// you'd convert them... 
						$filter_and_opts[1] = str_replace('{{', '[+', $filter_and_opts[1]);
						$filter_and_opts[1] = str_replace('}}', '+]', $filter_and_opts[1]);
						$options = explode('||',$filter_and_opts[1]);
						// avoid the array if not needed.
						if ($options[0] == $filter_and_opts[1]) {
							$options = $filter_and_opts[1];
						}
					}

					$new_value = CCTM::filter($value,$filter_and_opts[0], $options);

					// if we don't get a scalar, we skip that value.
					if (is_scalar($new_value)) {
						$value = $new_value;
					}

				}
				$tpl = str_replace('[+'.$complex_ph.'+]', $value, $tpl);
			}
		}
		else {
            CCTM::log(print_r(debug_backtrace(), true),__FILE__,__LINE__);
		}
		
		// Remove any unparsed [+placeholders+]
		if (!$preserve_unused_placeholders) {
			$tpl = preg_replace('/\[\+(.*?)\+\]/', '', $tpl);
		}
		return $tpl;
	}

	//------------------------------------------------------------------------------
	/**
	 * Custom joining on postmeta table for sorting on custom columns
	 */
	public static function posts_join($join) {

		global $wpdb;
	
		// We don't want searches
		if(is_search() ) {
			return $join;
		}
		
		$post_type = CCTM::get_value($_GET, 'post_type');
		if (empty($post_type)) {
			return $join;
		}
		if (isset(CCTM::$data['post_type_defs'][$post_type]['custom_orderby']) && !empty(CCTM::$data['post_type_defs'][$post_type]['custom_orderby'])) {
			$column = CCTM::$data['post_type_defs'][$post_type]['custom_orderby'];
			// Req'd to sort on custom column
			if (!in_array($column, CCTM::$reserved_field_names)) {
				$join .= $wpdb->prepare(" LEFT JOIN {$wpdb->postmeta} ON  {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = %s", $column);
			}
		}
			
		return $join;	
	}


	//------------------------------------------------------------------------------
	/**
	 * Print errors if they were thrown by the tests. Currently this is triggered as
	 * an admin notice so as not to disrupt front-end user access, but if there's an
	 * error, you should fix it! The plugin may behave erratically!
	 * INPUT: none... ideally I'd pass this a value, but the WP interface doesn't make
	 *  this easy, so instead I just read the class variable: CCTMtests::$errors
	 *
	 * @return none  But errors are printed if present.
	 */
	public static function print_notices() {
		if ( !empty(CCTM::$errors) ) {
			$error_items = '';
			foreach ( CCTM::$errors as $e ) {
				$error_items .= "<li>$e</li>";
			}
			$msg = sprintf( __('The %s plugin encountered errors! It cannot load!', CCTM_TXTDOMAIN)
				, CCTM::name);
			printf('<div id="custom-post-type-manager-warning" class="error">
				<p>
					<strong>%1$s</strong>
					<ul style="margin-left:30px;">
						%2$s
					</ul>
				</p>
				</div>'
				, $msg
				, $error_items);
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * Print warnings if there are any that haven't been dismissed
	 */
	public static function print_warnings() {

		$warning_items = '';

		// Check for warnings
		if ( !empty(self::$data['warnings']) ) {
			//   print '<pre>'. print_r(self::$data['warnings']) . '</pre>'; exit;
			$clear_warnings_url = sprintf(
				'<a href="?page=cctm&a=clear_warnings&_wpnonce=%s" title="%s" class="button">%s</a>'
				, wp_create_nonce('cctm_clear_warnings')
				, __('Dismiss all warnings', CCTM_TXTDOMAIN)
				, __('Dismiss Warnings', CCTM_TXTDOMAIN)
			);
			$warning_items = '';
			foreach ( self::$data['warnings'] as $warning => $viewed ) {
				if ($viewed == 0) {
					$warning_items .= "<li>$warning</li>";
				}
			}
		}

		if ($warning_items) {
			$msg = __('The Custom Content Type Manager encountered the following warnings:', CCTM_TXTDOMAIN);
			printf('<div id="custom-post-type-manager-warning" class="error">
				<p>
					<strong>%s</strong>
					<ul style="margin-left:30px;">
						%s
					</ul>
				</p>
				<p>%s</p>
				</div>'
				, $msg
				, $warning_items
				, $clear_warnings_url
			);
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * Performs a Javascript redirect in order to refresh the page. The $url should
	 * should include only query parameters and start with a ?, e.g. '?page=cctm'
	 *
	 * @param string $url the CCTM (admin_ page to redirect to.
	 * @param boolean $absolute if true, then the target URL is absolute (not in the mgr)
	 * @return none; this prints the result.
	 */
	public static function redirect($url, $absolute=false) {
		if ($absolute) {
			print '<script type="text/javascript">window.location.replace("'.$url.'");</script>';
		}
		else {
			print '<script type="text/javascript">window.location.replace("'.get_admin_url(false, 'admin.php').$url.'");</script>';
		}
		exit;
	}


	//------------------------------------------------------------------------------
	/**
	 * Register custom post-types, one by one. Data is stored in the wp_options table
	 * in a structure that matches exactly what the register_post_type() function
	 * expectes as arguments.
	 *
	 * See: http://codex.wordpress.org/Function_Reference/register_post_type
	 * See wp-includes/posts.php for examples of how WP registers the default post types
	 *
	 * $def = Array
	 * (
	 *     'supports' => Array
	 *         (
	 *             'title',
	 *             'editor'
	 *         ),
	 *
	 *     'post_type' => 'book',
	 *     'singular_label' => 'Book',
	 *     'label' => 'Books',
	 *     'description' => 'What I&#039;m reading',
	 *     'show_ui' => 1,
	 *     'capability_type' => 'post',
	 *     'public' => 1,
	 *     'menu_position' => '10',
	 *     'menu_icon' => '',
	 *     'custom_content_type_mgr_create_new_content_type_nonce' => 'd385da6ba3',
	 *     'Submit' => 'Create New Content Type',
	 *     'show_in_nav_menus' => '',
	 *     'can_export' => '',
	 *     'is_active' => 1,
	 * );
	 * FUTURE??:
	 * register_taxonomy( $post_type,
	 * $cpt_post_types,
	 * array( 'hierarchical' => get_disp_boolean($cpt_tax_type["hierarchical"]),
	 * 'label' => $cpt_label,
	 * 'show_ui' => get_disp_boolean($cpt_tax_type["show_ui"]),
	 * 'query_var' => get_disp_boolean($cpt_tax_type["query_var"]),
	 * 'rewrite' => array('slug' => $cpt_rewrite_slug),
	 * 'singular_label' => $cpt_singular_label,
	 * 'labels' => $cpt_labels
	 * ) );
	 */
	public static function register_custom_post_types() {

		$post_type_defs = self::get_post_type_defs();

		foreach ($post_type_defs as $post_type => $def) {
			$def = self::_prepare_post_type_def($def);
			
			if ( isset($def['is_active'])
				&& !empty($def['is_active'])
				&& !in_array($post_type, self::$built_in_post_types)
				&& isset($def['post_type'])
				) {
//                if (!isset($def['hierarchical']))
//                {
//                    print 'NOT '; exit;
//                }
//                else {
//                    print 'SET'; print '<pre>'.print_r($def, true); exit;
//                }
                //CCTM::log(print_r($def,true));
				register_post_type( $post_type, $def );
			}
		}
		// flush_rules moved to CCTM_PostTypeDef
	}


	//------------------------------------------------------------------------------
	/**
	 * Warnings are like errors, but they can be dismissed.
	 * So if the warning hasn't been logged already and dismissed,
	 * it gets its own place in the data structure.
	 *
	 * @param string  Text of the warning
	 * @return none
	 */
	public static function register_warning($str) {
		if (!empty($str) && !isset(self::$data['warnings'][$str])) {
			self::$data['warnings'][$str] = 0; // 0 = not read.
			update_option(self::db_key, self::$data);
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * We use this filter to customize the posts returned during an archive and during
	 * an RSS feed so that archives and RSS feeds can return custom post-types.
	 *
	 * See issue 13 for full archive suport:
	 * http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=13
	 * and http://bajada.net/2010/08/31/custom-post-types-in-the-loop-using-request-instead-of-pre_get_posts
	 *
	 * @param array $query
	 * @return array
	 */
	public static function request_filter( $query ) {

		// This is a troublesome little query... we need to monkey with it so WP will play nice with
		// custom post types, but if you breathe on it wrong, chaos ensues. See the following issues:
		//  http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=108
		//  http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=111
		//  http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=112
		//  http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=360
		// 	http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=458
		// https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=493
		if ( substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')+1) == 'edit.php' 
			&& self::get_value($_GET, 'post_type')) {
			return $query;
		}
		
		// Control what shows up in the RSS feed
		if (isset($query['feed'])) {
			$args = array( 'public' => true); // array('exclude_from_search'=>false); // ugh. WP has bad support here.
			$post_types = get_post_types($args);
			unset($post_types['revision']);
			unset($post_types['nav_menu_item']);
			
			CCTM::log('Request post-types:'. print_r($post_types, true),__FILE__,__LINE__);

			foreach ($post_types as $pt) {
				if('page' == $pt && self::get_setting('pages_in_rss_feed')) {
					// Leave pages in.
				}
				elseif($pt == 'post') {
					// Do nothing.  Posts are always included in the RSS feed.
				}
				// Exclude it if it was specifically excluded.
				elseif (!isset(self::$data['post_type_defs'][$pt]['include_in_rss']) || !self::$data['post_type_defs'][$pt]['include_in_rss']) {
					unset($post_types[$pt]);
				}
			}
			$query['post_type'] = $post_types;

		}
		// Handle Year/Month Archives
		elseif (!isset($query['post_type']) && isset($query['year']) && isset($query['monthnum'])) {
			// Get only public, custom post types
			$args = array( 'public' => true, '_builtin' => false );
			$public_post_types = get_post_types( $args );

			// Only posts get archives, not pages, so our first archivable post-type is "post"...
			$search_me_post_types = array('post');
	
			// check which have 'has_archive' enabled.
			foreach (self::$data['post_type_defs'] as $post_type => $def) {
				if ( isset($def['has_archive']) && $def['has_archive'] && in_array($post_type, $public_post_types)) {
					$search_me_post_types[] = $post_type;
				}
			}
	
			$query['post_type'] = $search_me_post_types;
	

		}
		// Ensure category pages show all available post-types
		elseif (!isset($query['post_type']) && ((isset($query['category_name']) && !empty($query['category_name'])) 
			|| (isset($query['cat']) && !empty($query['cat'])))) {
			if (!isset($query['page'])) { // <-- on a true category page, this won't be set
				$args = array( 'public' => true, '_builtin' => false );
				$public_post_types = get_post_types( $args );
	
				// Only posts get categories, not pages, so our first post-type is "post"...  has_archive is not enabled.
				$search_me_post_types = array('post');
		
				foreach (self::$data['post_type_defs'] as $post_type => $def) {
					if ( isset($def['taxonomies']) && is_array($def['taxonomies']) && in_array('category', $def['taxonomies'])) {
						$search_me_post_types[] = $post_type;
					}
				}
						
				$query['post_type'] = $search_me_post_types;
			}
		}
		// Handle tag pages
		elseif (!isset($query['post_type']) && isset($query['tag'])) {
			$args = array( 'public' => true, '_builtin' => false );
			$public_post_types = get_post_types( $args );

			// Only posts get archives, not pages, so our first archivable post-type is "post"...
			$search_me_post_types = array('post');
	
			// check which have 'has_archive' enabled.
			foreach (self::$data['post_type_defs'] as $post_type => $def) {
				if ( isset($def['taxonomies']) && is_array($def['taxonomies']) && in_array('post_tag', $def['taxonomies'])) {
					$search_me_post_types[] = $post_type;
				}
			}
	
			$query['post_type'] = $search_me_post_types;		
		}

	
		return $query;
	}


	//------------------------------------------------------------------------------
	/**
	 * Adds custom post-types to dashboard "Right Now" widget
	 */
	public static function right_now_widget() {
	
		if (!self::get_setting('enable_right_now')) {
			return;
		}
		
		$args = array(
			'public' => true ,
			'_builtin' => false
		);
		$output = 'object';
		$operator = 'and';

		$post_types = get_post_types( $args , $output , $operator );

		foreach ( $post_types as $post_type ) {
			//die(print_r($post_type, true));
			if (isset(self::$data['post_type_defs'][$post_type->name]['cctm_enable_right_now']) && !self::$data['post_type_defs'][$post_type->name]['cctm_enable_right_now']) {
				continue;
			}
			
			$num_posts = wp_count_posts( $post_type->name );
			$num = number_format_i18n( $num_posts->publish );
			$text = _n( $post_type->labels->singular_name, $post_type->labels->name , intval( $num_posts->publish ) );

			// Make links if the user has permission to edit
			if ( current_user_can( 'edit_posts' ) ) {
				$num = "<a href='edit.php?post_type=$post_type->name'>$num</a>";
				$text = "<a href='edit.php?post_type=$post_type->name'>$text</a>";
			}
			printf('<tr><td class="first b b-%s">%s</td>', $post_type->name, $num);
			printf('<td class="t %s">%s</td></tr>', $post_type->name, $text);
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Ensures that the front-end search form can find posts or view posts in the RSS
	 * CONFUSED: Looks like only the request_filter handles the RSS stuff... and why is the 
	 * $query variable here an object, whereas in the request_filter it's an array?
	 * http://mysite.com/category/my_cat/ does not seem to trigger this filter anymore.
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=143
	 * See also http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=186
	 *
	 * @param string $query
	 * @return string
	 */
	public static function search_filter($query) {
//		die(print_r($query,true));
		// See the following bugs:
		// http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=349
		// http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=366
		if ($query->is_feed) {
			if ( !isset($_GET['post_type']) && empty($_GET['post_type'])
			&& !isset($query->query_vars['post_type'])
				&& empty($query->query_vars['post_type'])) {
				$args = array('exclude_from_search'=>false); // array( 'public' => true);
				$post_types = get_post_types($args);
				unset($post_types['revision']);
				unset($post_types['nav_menu_item']);
//				unset($post_types['page']); // TO-DO: configure this?
				foreach ($post_types as $pt) {
					// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=412
					if('page' == $pt && self::get_setting('pages_in_rss_feed')) {
						// Leave pages in.
					}
					elseif($pt == 'post') {
						// Do nothing.  Posts are always included in the RSS feed.
					}
					// Exclude it if it was specifically excluded.
					elseif (!isset($pt['include_in_rss']) || !$pt['include_in_rss']) {
						unset($post_types[$key]);
					}
				}
				// The format of the array of $post_types is array('post' => 'post', 'page' => 'page')
				$query->set('post_type', $post_types);
			}
		}
		// See issue http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=330
		elseif ($query->is_search || $query->is_category) {
			if ( !isset($_GET['post_type']) && empty($_GET['post_type']) 
				&& !isset($query->query_vars['post_type'])
				&& empty($query->query_vars['post_type'])) {
				$post_types = get_post_types( array('exclude_from_search'=>false) );
				// The format of the array of $post_types is array('post' => 'post', 'page' => 'page')
				$query->set('post_type', $post_types);
			}
		}
		
		CCTM::log('search_filter '.print_r($query->get('post_type'), true),__FILE__,__LINE__);
			
		return $query;
	}


	//------------------------------------------------------------------------------
	/**
	 * Sets a flash message that's viewable only for the next page view (for the current user)
	 * $_SESSION doesn't work b/c WP doesn't natively support them = lots of confused users.
	 * setcookie() won't work b/c WP has already sent header info.
	 * So instead, we store this stuff in the database. Sigh.
	 *
	 * @param string  $msg text or html message
	 */
	public static function set_flash($msg) {
		self::$data['flash'][ self::get_user_identifier() ] = $msg;
		update_option(self::db_key, self::$data);
	}


	//------------------------------------------------------------------------------
	/**
	 * Used by php usort to sort custom field defs by their sort_param attribute
	 *
	 * @param string  $field
	 * @param string  $sortfunc
	 * @return array
	 */
	public static function sort_custom_fields($field, $sortfunc) {
		return create_function('$var1, $var2', 'return '.$sortfunc.'($var1["'.$field.'"], $var2["'.$field.'"]);');
	}


	//------------------------------------------------------------------------------
	/**
	 * Recursively removes all quotes from $_POSTED data if magic quotes are on
	 * http://algorytmy.pl/doc/php/function.stripslashes.php
	 *
	 * @param array   possibly nested
	 * @param mixed $value
	 * @return array clensed of slashes
	 */
	public static function stripslashes_deep($value) {
		if ( is_array($value) ) {
			$value = array_map( 'CCTM::'. __FUNCTION__, $value);
		}
		else {
			$value = stripslashes($value);
		}
		return $value;
	}


	//------------------------------------------------------------------------------
	/**
	 * Recursively strips tags from all inputs, including nested ones.
	 *
	 * @param mixed $value
	 * @return array the input array, with tags stripped out of each value.
	 */
	public static function striptags_deep($value) {
		if ( is_array($value) ) {
			$value = array_map('CCTM::'. __FUNCTION__, $value);
		}
		else {
			$value = strip_tags($value, self::$allowed_html_tags);
		}
		return $value;
	}

	//------------------------------------------------------------------------------
	/**
	 * Adds the button to the TinyMCE 1st row.
	 */
	public static function tinyplugin_add_button($buttons) {
	    array_push($buttons, '|', 'custom_fields');
	    return $buttons;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * This is for the "Custom Fields" tinyMCE button.
	 */
	public static function tinyplugin_register($plugin_array) {
	    $url = CCTM_URL.'/js/plugins/custom_fields.js';
	    $plugin_array['custom_fields'] = $url;
	    return $plugin_array;
	}
}


/*EOF CCTM.php*/
