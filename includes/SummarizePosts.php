<?php
/**
 * SummarizePosts
 *
 * Handles the 'summarize-posts' shortcode and related template tags.  This is a 
 * convenience class that wraps the GetPostsQuery class.  It's used to handle
 * shortcodes and other simplified interpretations of the powerful GetPostsquery class.
 *
 * @package SummarizePosts
 */


class SummarizePosts {
	const name    = 'Summarize Posts';
	const version   = '0.8';
	// See http://php.net/manual/en/function.version-compare.php
	// any string not found in this list < dev < alpha =a < beta = b < RC = rc < # < pl = p
	const version_meta  = 'dev'; // dev, rc (release candidate), pl (public release)

	const wp_req_ver  = '3.1';
	const php_req_ver  = '5.2.6';
	const mysql_req_ver = '5.0.0';

	// used in the wp_options table
	const db_key   = 'summarize_posts';
	const admin_menu_slug  = 'summarize_posts';


	// The default options after being read from get_option()
	public static $options;

	const txtdomain  = 'summarize-posts';

	const result_tpl = '<li><a href="[+permalink+]">[+post_title+]</a></li>';

	// One placeholder can be designated
	public static $help_placeholder = '[+help+]';

	// These are defaults for OTHER settings, outside of the get_posts()
	public static $formatting_defaults = array(
		'before'   => '<ul class="summarize-posts">',
		'after'   => '</ul>',
		'tpl'   => null,
		'help'   => false,
	);

	//------------------------------------------------------------------------------
	//! Private functions
	//------------------------------------------------------------------------------
	/**
	 * Get the formatting string (tpl) to format each search result in a short-code
	 * The $args can supply a 'tpl' key that defines a .tpl file.
	 *
	 * @param string  $content
	 * @param array   $args    associative array
	 * @return string
	 */
	private static function _get_tpl($content, $args) {

		if ( isset($args['tpl']) && !empty($args['tpl']) && preg_match('/\.tpl$/i', $args['tpl'])) {
			// strip possible leading slash
			$args['tpl'] = preg_replace('/^\//', '', $args['tpl']);
			$file = ABSPATH .$args['tpl'];

			if ( file_exists($file) ) {
				return file_get_contents($file);
			}
			else {
				return sprintf(__('.tpl file does not exist %s', CCTM_TXTDOMAIN), $args['tpl']);
			}
		}
		// Read from between [summarize-posts]in between[/summarize-posts]
		else {
			$content = trim($content);
			$content = html_entity_decode($content);
			// fix the quotes back to normal
			$content = str_replace(array('&#8221;', '&#8220;'), '"', $content );
			return str_replace(array('&#8216;', '&#8217;'), "'", $content );
		}
	}

	//------------------------------------------------------------------------------
	//! Public Functions
	//------------------------------------------------------------------------------
	/**
	 * Create custom post-type menu
	 * @return void
	 */
	public static function create_admin_menu() {
		add_options_page(
			'Summarize Posts',      // page title
			'Summarize Posts',      // menu title
			'manage_options',      // capability
			self::admin_menu_slug,     // menu slug
			'SummarizePosts::get_admin_page' // callback
		);
	}

	//------------------------------------------------------------------------------
	/**
	 * Get from Array. Safely retrieves a value from an array, bypassing the 'isset()'
	 * errors.
	 *
	 * @param array $array the array to be searched
	 * @param string $key the place in the $array to return (if available)
	 * @param mixed $default (optional) value to return if that spot in the array is not set
	 * @return mixed either the value stored @ $key, or the $default
	 */
	public static function get_from_array($array, $key, $default='') {
		if ( isset($array[$key]) ) {
			return $array[$key];
		}
		else {
			return $default;
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * Retrieves a complete post object, including all meta fields.
	 * Ah... get_post_custom() will treat each custom field as an array, because in WP
	 * you can tie multiple rows of data to the same fieldname (which can cause some
	 * architectural related headaches).
	 *
	 * At the end of this, I want a post object that can work like this:
	 *
	 * print $post['post_title'];
	 * print $post['my_custom_field']; // not $post->my_custom_fields[0];
	 *
	 * @param integer $id valid ID of a post (regardless of post_type).
	 * @return array post arra with all attributes, including custom fields.
	 */
	public function get_post_complete($id) {
		$complete_post = get_post($id, self::$options['output_type']);
		if ( empty($complete_post) ) {
			return array();
		}
		$custom_fields = get_post_custom($id);
		if (empty($custom_fields)) {
			return $complete_post;
		}
		foreach ( $custom_fields as $fieldname => $value ) {
			$complete_post[$fieldname] = $value[0];
		}

		return $complete_post;
	}

	//------------------------------------------------------------------------------
	/**
	 * http://codex.wordpress.org/Template_Tags/get_posts
	 * sample usage
	 * shortcode params:
	 * 'numberposts'     => 5,
	 * 'offset'          => 0,
	 * 'category'        => ,
	 * 'orderby'         => any valid column from the wp_posts table (minus the "post_")
	 * ID
	 * author
	 * date
	 * date_gmt
	 * content
	 * title
	 * excerpt
	 * status
	 * comment_status
	 * ping_status
	 * password
	 * name
	 * to_ping
	 * pinged
	 * modified
	 * modified_gmt
	 * content_filtered
	 * parent
	 * guid
	 * menu_order
	 * type
	 * mime_type
	 * comment_count
	 * rand -- randomly sort results. This is not compatible with the paginate options! If set,
	 * the 'paginate' option will be ignored!
	 * 'order'           => 'DESC',
	 * 'include'         => ,
	 * 'exclude'         => ,
	 * 'meta_key'        => ,
	 * 'meta_value'      => ,
	 * 'post_type'       => 'post',
	 * 'post_mime_type'  => ,
	 * 'post_parent'     => ,
	 * 'post_status'     => 'publish'
	 * * CUSTOM **
	 * before
	 * after
	 * paginate true|false
	 * placeholders:
	 * [+help+]
	 * [shortcode x="1" y="2"]<ul>Formatting template goes here</ul>[/shortcode]
	 * The $content comes from what's between the tags.
	 * A standard post has the following attributes:
	 * [ID] => 6
	 * [post_author] => 2
	 * [post_date] => 2010-11-13 20:13:28
	 * [post_date_gmt] => 2010-11-13 20:13:28
	 * [post_content] => http://pretasurf.com/blog/wp-content/uploads/2010/11/cropped-LIFE_04_DSC_0024.bw_.jpg
	 * [post_title] => cropped-LIFE_04_DSC_0024.bw_.jpg
	 * [post_excerpt] =>
	 * [post_status] => inherit
	 * [comment_status] => closed
	 * [ping_status] => open
	 * [post_password] =>
	 * [post_name] => cropped-life_04_dsc_0024-bw_-jpg
	 * [to_ping] =>
	 * [pinged] =>
	 * [post_modified] => 2010-11-13 20:13:28
	 * [post_modified_gmt] => 2010-11-13 20:13:28
	 * [post_content_filtered] =>
	 * [post_parent] => 0
	 * [guid] => http://pretasurf.com/blog/wp-content/uploads/2010/11/cropped-LIFE_04_DSC_0024.bw_.jpg
	 * [menu_order] => 0
	 * [post_type] => attachment
	 * [post_mime_type] => image/jpeg
	 * [comment_count] => 0
	 * [filter] => raw
	 * But notice that some of these are not very friendly.  E.g. post_author, the user expects the author's name.  So we do some duplicating, tweaking to make this easier on the user.
	 * Placeholders:
	 * Generally, these correspond to the names of the database columns in the wp_posts table, but some
	 * convenience placeholders were added.
	 * drwxr-xr-x   8 everett2  staff   272 Feb  5 20:16 .
	 * [+ID+]
	 * [+post_author+]
	 * [+post_date+]
	 * [+post_date_gmt+]
	 * [+post_content+]
	 * [+post_title+]
	 * [+post_excerpt+]
	 * [+post_status+]
	 * [+comment_status+]
	 * [+ping_status+]
	 * [+post_password+]
	 * [+post_name+]
	 * [+to_ping+]
	 * [+pinged+]
	 * [+post_modified+]
	 * [+post_modified_gmt+]
	 * [+post_content_filtered+]
	 * [+post_parent+]
	 * [+guid+]
	 * [+menu_order+]
	 * [+post_type+]
	 * [+post_mime_type+]
	 * [+comment_count+]
	 * [+filter+]
	 * Convenience:
	 * [+permalink+]
	 * [+the_content+]
	 * [+the_author+]
	 * [+title+]
	 * [+date+]
	 * [+excerpt+]
	 * [+mime_type+]
	 * [+modified+]
	 * [+parent+]
	 * [+modified_gmt+]
	 * ;
	 *
	 * @param array $raw_args    (optional)
	 * @param string $content_str (optional)
	 * @return array
	 */
	public static function get_posts($raw_args=array(), $content_str = null) {

		// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=389
		$content_str = preg_replace('#^</p>#', '', $content_str);
		$content_str = preg_replace('#<p>$#', '', $content_str);
		
		$content_str = trim($content_str);
		if ( empty($content_str) ) {
			$content_str = self::result_tpl; // default
		}

		if (empty($raw_args) || !is_array($raw_args)) {
			$raw_args = array();
		}

		$formatting_args = shortcode_atts(self::$formatting_defaults, $raw_args);
		$formatting_args['tpl_str'] = self::_get_tpl($content_str, $formatting_args);

		$help_flag = false;
		if (isset($raw_args['help']) ) {
			$help_flag = true;
			unset($raw_args['help']);
		}
		
		// see http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=427
        // https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=483
        foreach (self::$formatting_defaults as $k => $v) {
            unset($raw_args[$k]);
        }
        
		$output = '';

		$Q = new GetPostsQuery();
		$args = array_merge($Q->defaults, $raw_args);

        if ($args['paginate']) {
            require_once dirname(__FILE__).'/CCTM_Pagination.conf.php';
            $C = new CCTM_Pagination_Configuration();
            $tpls = $C->tpls['default'];
            $args['offset'] = (int) (isset($_GET['offset'])) ? $_GET['offset'] : '';
            $Q->set_tpls($tpls);
        }

		$results = $Q->get_posts($args);

		// Print help message.  Should include the SQL statement, errors
		if ($help_flag) {
			return $Q->debug(); // this prints the results
		}

		if (empty($results)) {
			return '';
		}
		
		$output = $formatting_args['before'];
		foreach ( $results as $r ) {
			$output .= CCTM::parse($formatting_args['tpl_str'], $r);
		}
		$output .= $formatting_args['after'];

		if ( $args['paginate'] ) {
			$output .= '<div class="summarize-posts-pagination-links">'.$Q->get_pagination_links().'</div>';
		}


		return $output;
	}

	//------------------------------------------------------------------------------
	/**
	 * Rewrite of built-in get_categories() function.
	 *
	 * http://codex.wordpress.org/Function_Reference/get_categories
	 * see wp-includes/category.php
	 * Adds a few extra attributes to the output.
	 * Returns an array of Ojects, e.g.
	 * Array
	 * (
	 * [0] => stdClass Object
	 * (
	 * [term_id] => 1
	 * [name] => Uncategorized
	 * [slug] => uncategorized
	 * [term_group] => 0
	 * [term_taxonomy_id] => 1
	 * [taxonomy] => category
	 * [description] =>
	 * [parent] => 0
	 * [count] => 1
	 * [cat_ID] => 1
	 * [category_count] => 1
	 * [category_description] =>
	 * [cat_name] => Uncategorized
	 * [category_nicename] => uncategorized
	 * [category_parent] => 0
	 * [permalink] => http://pretasurf:8888/?taxonomy=collection&term=spring_summer_2011
	 * [is_active] => 1
	 * )
	 * )
	 *
	 * @param array $args (optional)
	 * @return array
	 */
	public static function get_taxonomy_terms( $args = '' ) {
		// get_categories() defaults
		$defaults = array(
			'type'                     => 'post',
			'child_of'                 => 0,
			'parent'                   => null,
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 1,
			'hierarchical'             => 1,
			'exclude'                  => null,
			'include'                  => null,
			'number'                   => null,
			'taxonomy'                 => 'category',
			'pad_counts'               => false
		);

		// We use both so we can parse the URL type inputs that come in as a string.
		$args = wp_parse_args($args, $defaults); // This converts the input to an array
		$args = shortcode_atts($defaults, $args); // This will filter out invalid input

		$active_taxonomy = get_query_var('taxonomy');
		$active_slug = get_query_var('term');

		$taxonomies = get_categories($args);

		// Add a few custom attributes for convenience
		foreach ( $taxonomies as &$t ) {
			$t->permalink = home_url("?taxonomy=$t->taxonomy&amp;term=$t->slug");
			if ( $t->slug == $active_slug ) {
				$t->is_active = true;
			}
			else {
				$t->is_active = false;
			}
		}

		return $taxonomies;

	}


	//------------------------------------------------------------------------------
	/**
	 * Retrieve the post content and chop it off at the marker specified.  OMFG WP is
	 * so F'd up here. No reason to copy this function from wp-includes/post-template.php
	 * because the built-in function is a total mess.
	 * 
	 * The goal is to make this damn thing loop-agnostic.  Remove all the F'ing global variables.
	 * the_content('read more &raquo;'); // This ignores the <!--more--> bit if used in a single template file. *facepalm*
	 * the <!--more--> bit is translated to <span id="more-524"></span> where 524 is the post id.
	 * This is my home-rolled version of how the_content() works.
	 *
	 * @param integer $id
	 * @param string $content
	 * @param string $more_link_text (optional)
	 * @param integer $stripteaser    (optional)
	 * @return string
	 */
	static function get_the_content($id, $content, $more_link_text = null, $stripteaser = 0) {

		// $content = get_the_content( 'read more &raquo;');
		//print $content;
		// $post_id = get_the_ID();
		//print $post_id; exit;
		// $more = '<span id="more-'.$post_id.'"></span>';
		$more = '<span id="more';

		$content = preg_replace('/'.$more.'.*$/ms', '', $content);
		return $content;

		//$content = $content . '<a href="'.get_permalink($id).'">read more &raquo;</a>';

		return $content;
	}

	//------------------------------------------------------------------------------
	/**
	 * Print errors if they were thrown by the tests. Currently this is triggered as
	 * an admin notice so as not to disrupt front-end user access, but if there's an
	 * error, you should fix it! The plugin may behave erratically!
	 *
	 * @return void  errors are printed if present.
	 */
	public static function print_notices() {
		if ( !empty(SummarizePostsTests::$errors) ) {

			$error_items = '';
			foreach ( SummarizePostsTests::$errors as $e ) {
				$error_items .= "<li>$e</li>";
			}

			$msg = sprintf( __('The %s plugin encountered errors! It cannot load!', self::txtdomain)
				, self::name);

			printf('<div id="summarize-posts-warning" class="error">
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
	 * This is the tie-into the GetPostsForm object: it returns (not prints) a form
	 * OR it handles form submissions and returns results.
	 * 
	 * @param array $args        (optional) defines which controls should be displayed
	 * @param string $content_tpl (optional) passed to the get_posts() function, this defines how each result will be formatted.
	 * @return string HTML form, or HTML results if the form was property submitted.
	 */
	public static function search($args=array(), $content_tpl = null) {
		$Form = new GetPostsForm($args);

		$nonce = self::get_from_array($_POST, $Form->nonce_name);
		// Draw the search form
		if (empty($_POST)) {
			return $Form->generate();
		}
		elseif (wp_verify_nonce($nonce, $Form->nonce_action) ) {
			unset($_POST[$Form->nonce_name]);
			unset($_POST['_wp_http_referer']);
			$search_args = array();
			foreach ($_POST as $k => $v) {
				// Strip the prefix
				$new_key = preg_replace('/^'.$Form->name_prefix.'/', '', $k);
				$search_args[$new_key] = $v;
			}

			$results = self::get_posts($search_args);
			if  (empty($results)) {
				print $Form->get_no_results_msg();
			}
			else {
				print $results;
			}
		}
		else {
			return "Invalid Submission.";
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Adds the button to the TinyMCE 1st row.
	 *
	 * @param array $buttons
	 * @return array
	 */
	public static function tinyplugin_add_button($buttons) {
		array_push($buttons, '|', 'summarize_posts');
		return $buttons;
	}


	//------------------------------------------------------------------------------
	/**
	 * 
	 *
	 * @param array $plugin_array
	 * @return array
	 */
	public static function tinyplugin_register($plugin_array) {
		$url = CCTM_URL.'/js/editor_plugin.js';
		$plugin_array['summarize_posts'] = $url;
		return $plugin_array;
	}


}


/*EOF*/