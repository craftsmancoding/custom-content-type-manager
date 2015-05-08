<?php
/**
 * Library used by the create_post_type.php and edit_post_type.php controllers. 
 * I've offloaded functions from the main CCTM class to here because they're only
 * used in these certain situations.
 */
class CCTM_PostTypeDef {

	/**
	 * Get all available columns (i.e. fields) for this post_type, used for 
	 * showing which columns to include.
	 *
	 * @param	string	$post_type
	 * @param	string	HTML output (table rows)
	 */
	public static function get_columns($post_type) {
	
		$output = '';
		//$built_in_columns = CCTM::$reserved_field_names;
		$built_in_columns = array(
			//'cb' => '<input type="checkbox" />',
			'title' => __('Title'), // post_title
			'author' => __('Author'), // lookup on wp_users
			'comments' => __('Comments'),
			'date' => __('Date')
		);
		
		
		$custom_fields = array();
		if (isset(CCTM::$data['post_type_defs'][$post_type]['custom_fields'])) {
			$custom_fields = CCTM::$data['post_type_defs'][$post_type]['custom_fields'];
		}
		$taxonomies = array();
		if (isset(CCTM::$data['post_type_defs'][$post_type]['taxonomies'])) {
			$taxonomies = CCTM::$data['post_type_defs'][$post_type]['taxonomies'];
		}
		
		
		// Get selected columns (float to top)
		$custom_columns = array();
		if (isset(CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns'])) {
			$custom_columns = CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns'];
		}

		foreach ($custom_columns as $c) {
			$d = array();
			if (in_array($c, array_keys($built_in_columns))) {
				$d['name'] = $c;
				$d['label'] = $built_in_columns[$c]; 
				$d['class'] = 'cctm_builtin_column';
				$d['description'] = __('Built-in WordPress column.', CCTM_TXTDOMAIN);
			}
			elseif(in_array($c, $taxonomies)) {
				$t = get_taxonomy($c);
				if (isset($t)) {
					$d['name'] = $c; 
					$d['label'] = __($t->labels->singular_name);
					$d['class'] = 'cctm_taxonomy_column';
					$d['description'] = __('WordPress Taxonomy', CCTM_TXTDOMAIN);						
				}
			}
			elseif(in_array($c, $custom_fields)) {
				if (isset(CCTM::$data['custom_field_defs'][$c])) {
					$d['name'] = CCTM::$data['custom_field_defs'][$c]['name'];
					$d['label'] = CCTM::$data['custom_field_defs'][$c]['label'];
					$d['class'] = 'cctm_custom_column';
					$d['description'] = CCTM::$data['custom_field_defs'][$c]['description'];			
				}			
			}
			else {
				continue;
			}
			$d['is_checked'] = 'checked="checked"';		
			$output .= CCTM::load_view('tr_column.php', $d);		
		}
		
		// Separator
		$output .= '<tr class="no-sort"><td colspan="4" style="background-color:#ededed;"><hr /></td></tr>';

		
		// Get built-in columns		
		foreach ($built_in_columns as $c => $label) {
			if (in_array($c, $custom_columns)) {
				continue;
			}
			$d = array();
			$d['name'] = $c;
			$d['label'] = $label;
			$d['class'] = 'cctm_builtin_column';
			$d['description'] = __('Built-in WordPress column.', CCTM_TXTDOMAIN);
			$d['is_checked'] = '';
		
			$output .= CCTM::load_view('tr_column.php', $d);
		}

		
		// Get custom fields
		foreach ($custom_fields as $c) {
			if (in_array($c, $custom_columns)) {
				continue;
			}
			if (isset(CCTM::$data['custom_field_defs'][$c])) {
				$d = array();				
				$d['name'] = CCTM::$data['custom_field_defs'][$c]['name'];	
				$d['label'] = CCTM::$data['custom_field_defs'][$c]['label'];	
				$d['class'] = 'cctm_custom_column';
				$d['description'] = CCTM::$data['custom_field_defs'][$c]['description'];			
				$d['is_checked'] = '';

				$output .= CCTM::load_view('tr_column.php', $d);
					
			}
		}
		
		// Get taxonomies
		foreach ($taxonomies as $taxonomy) {
			if (in_array($taxonomy, $custom_columns)) {
				continue;
			}
			$t = get_taxonomy($taxonomy);
			if (isset($t)) {
//				die(print_r($t,true));
				$d['name'] = $taxonomy; //$t->labels->singular_name;	
				$d['label'] = $t->labels->singular_name;
				$d['class'] = 'cctm_taxonomy_column';
				$d['description'] = __('WordPress Taxonomy', CCTM_TXTDOMAIN);
				$d['is_checked'] = '';

				$output .= CCTM::load_view('tr_column.php', $d);		
			}
		}		

		return $output;	
		
	}

	/**
	 * Get all available columns (i.e. fields) for this post_type, used for 
	 * showing which column to sort by.
	 *
	 
			<option value=""><?php _e('Default', CCTM_TXTDOMAIN); ?></option>
			<option value="ID" <?php 			print CCTM::is_selected('ID',$data['def']['custom_orderby']); ?>>ID</option>
			<option value="post_author" <?php 	print CCTM::is_selected('post_author',$data['def']['custom_orderby']); ?>>post_author</option>
			<option value="post_date" <?php 	print CCTM::is_selected('post_date',$data['def']['custom_orderby']); ?>>post_date</option>
			<option value="post_content" <?php 	print CCTM::is_selected('post_content',$data['def']['custom_orderby']); ?>>post_content</option>
			<option value="post_title" <?php 	print CCTM::is_selected('post_title',$data['def']['custom_orderby']); ?>>post_title</option>
			<option value="post_excerpt" <?php 	print CCTM::is_selected('post_excerpt',$data['def']['custom_orderby']); ?>>post_excerpt</option>
			<option value="post_status" <?php 	print CCTM::is_selected('post_status',$data['def']['custom_orderby']); ?>>post_status</option>
			<option value="post_modified" <?php print CCTM::is_selected('post_modified',$data['def']['custom_orderby']); ?>>post_modified</option>
			<option value="post_parent" <?php 	print CCTM::is_selected('post_parent',$data['def']['custom_orderby']); ?>>post_parent</option>
			<option value="menu_order" <?php 	print CCTM::is_selected('menu_order',$data['def']['custom_orderby']); ?>>menu_order</option>
			<option value="post_type" <?php 	print CCTM::is_selected('post_type',$data['def']['custom_orderby']); ?>>post_type</option>
			<option value="comment_count" <?php 	print CCTM::is_selected('comment_count',$data['def']['custom_orderby']); ?>>comment_count</option>	 
	 * @param	string	$post_type
	 * @param	string	HTML output (dropdown options)
	 */
	public static function get_orderby_options($post_type) {

		$output = '<option value="">'.__('Default', CCTM_TXTDOMAIN).'</option>';
		
		$built_in_columns = CCTM::$reserved_field_names;

		foreach ($built_in_columns as $c) {
			$is_selected = '';
			if (isset(CCTM::$data['post_type_defs'][$post_type]['custom_orderby'])
				&& CCTM::$data['post_type_defs'][$post_type]['custom_orderby'] == $c) {
				$is_selected = ' selected="selected"';
			}
			$output .= sprintf('<option value="%s" %s>%s</option>', $c, $is_selected, __($c));
		}
		
		$custom_fields = array();
		if (isset(CCTM::$data['post_type_defs'][$post_type]['custom_fields'])) {
			$custom_fields = CCTM::$data['post_type_defs'][$post_type]['custom_fields'];		
		}
		
		
		// Get custom fields
		foreach ($custom_fields as $c) {
			$label = __($c);
			if (isset(CCTM::$data['custom_field_defs'][$c])) {	
				$label = __(CCTM::$data['custom_field_defs'][$c]['label']);	
			}
			
			$is_selected = '';
			if (isset(CCTM::$data['post_type_defs'][$post_type]['custom_orderby'])
				&& CCTM::$data['post_type_defs'][$post_type]['custom_orderby'] == $c) {
				$is_selected = ' selected="selected"';
			}
			$output .= sprintf('<option value="%s" %s>%s</option>', $c, $is_selected, $label);
	
		}
		
		return $output;			
	}

	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @return string representing all img tags for all post-type icons
	 */
	public static function get_post_type_icons() {

		$icons = array();
		if ($handle = opendir(CCTM_PATH.'/images/icons/16x16')) {
			while (false !== ($file = readdir($handle))) {
				if ( !preg_match('/^\./', $file) ) {
					$icons[] = $file;
				}
			}
			closedir($handle);
		}

		$output = '';

		foreach ( $icons as $img ) {
			$output .= sprintf('
				<span class="cctm-icon">
					<img src="%s" title="%s" onclick="javascript:send_to_menu_icon(\'%s\');"/>
				</span>'
				, CCTM_URL.'/images/icons/32x32/'.$img
				, $img
				, CCTM_URL.'/images/icons/16x16/'.$img
			);
		}

		return $output;
	}


	//------------------------------------------------------------------------------
	/**
	 * SYNOPSIS: checks the custom content data array to see $post_type exists as one
	 * of CCTM's defined post types (it doesn't check against post types defined
	 * elsewhwere).
	 *
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/wiki/DataStructures
	 *
	 * Built-in post types 'page' and 'post' are considered valid (i.e. existing) by
	 * default, even if they haven't been explicitly defined for use by this plugin
	 * so long as the 2nd argument, $search_built_ins, is not overridden to false.
	 * We do this because sometimes we need to consider posts and pages, and other times
	 * not.
	 *
	 * $built_in_post_types array.
	 *
	 * @param string  $post_type        the lowercase database slug identifying a post type.
	 * @param boolean $search_foreigns (optional) whether or not to search ANY defined post-type
	 * @return boolean indicating whether this is a valid post-type
	 */
	public static function is_existing_post_type($post_type, $search_built_ins=true) {

		// If there is no existing data, check against the built-ins
		if ( empty(CCTM::$data['post_type_defs']) && $search_built_ins ) {
			return in_array($post_type, CCTM::$built_in_post_types);
		}
		// If there's no existing $data and we omit the built-ins...
		elseif ( empty(CCTM::$data['post_type_defs']) && !$search_built_ins ) {
			return false;
		}
		// Check to see if we've stored this $post_type before
		elseif ( array_key_exists($post_type, CCTM::$data['post_type_defs']) ) {
			return true;
		}
		// Check the built-ins
		elseif ( $search_built_ins && in_array($post_type, get_post_types()) ) {
			return true;
		}
		else {
			return false;
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * Check for errors: ensure that $post_type is a valid post_type name.
	 *
	 * @param mixed   $data describes a post type (this will be input to the register_post_type() function
	 * @param boolean $new  (optional) whether or not the post_type is new (default=false)
	 * @return mixed  returns null if there are no errors, otherwise returns a string describing an error.
	 */
	public static function post_type_name_has_errors($data, $new=false) {

		$errors = null;

		$taxonomy_names_array = get_taxonomies('', 'names');

		if ( empty($data['post_type']) ) {
			return __('Name is required.', CCTM_TXTDOMAIN);
		}
		if ( empty($data['labels']['menu_name'])) // remember: the location in the $_POST array is different from the name of the option in the form-def.
			{
			return __('Menu Name is required.', CCTM_TXTDOMAIN);
		}

		foreach ( CCTM::$reserved_prefixes as $rp ) {
			if ( preg_match('/^'.preg_quote($rp).'.*/', $data['post_type']) ) {
				return sprintf( __('The post type name cannot begin with %s because that is a reserved prefix.', CCTM_TXTDOMAIN)
					, $rp);
			}
		}

		$registered_post_types = get_post_types();
		$cctm_post_types = array_keys(CCTM::$data['post_type_defs']); // this will include foreigns
		$other_post_types = array_diff($registered_post_types, $cctm_post_types);
		$other_post_types = array_diff($other_post_types, CCTM::$reserved_post_types);
		$dead_foreigners = array();
		foreach ($cctm_post_types as $pt) {
			if (isset(CCTM::$data['post_type_defs'][$pt]['is_foreign']) 
				&& CCTM::$data['post_type_defs'][$pt]['is_foreign']
				&& !in_array($pt, $registered_post_types)
				) {
				$dead_foreigners[] = $pt;
			}
		}
		
		// Is reserved name?
		if ( in_array($data['post_type'], CCTM::$reserved_post_types) ) {
			$msg = __('Please choose another name.', CCTM_TXTDOMAIN );
			$msg .= ' ';
			$msg .= sprintf( __('%s is a reserved name.', CCTM_TXTDOMAIN )
				, '<strong>'.$post_type.'</strong>' );
			return $msg;
		}
		// Make sure the post-type name does not conflict with any registered taxonomies
		elseif ( in_array( $data['post_type'], $taxonomy_names_array) ) {
			$msg = __('Please choose another name.', CCTM_TXTDOMAIN );
			$msg .= ' ';
			$msg .= sprintf( __('%s is already in use as a registered taxonomy name.', CCTM_TXTDOMAIN)
				, $post_type );
			return $msg;
		}
		// If this is a new post_type or if the $post_type name has been changed,
		// ensure that it is not going to overwrite an existing post type name.
		elseif ( $new && is_array(CCTM::$data['post_type_defs']) 
			&& in_array($data['post_type'], $cctm_post_types ) 
			&& !in_array($data['post_type'], $dead_foreigners)
			) {
			return sprintf( __('The name %s is already in use.', CCTM_TXTDOMAIN), htmlspecialchars($data['post_type']) );
		}
		// Is the name taken by an existing post type registered by some other plugin?
		elseif (in_array($data['post_type'], $other_post_types) ) {
			return sprintf( __('The name %s has been registered by some other plugin.', CCTM_TXTDOMAIN), htmlspecialchars($data['post_type']) );
		}
		// Make sure there's not an unsuspecting theme file named single-my_post_type.php
		/*
		$dir = get_stylesheet_directory();
		if ( file_exists($dir . '/single-'.$data['post_type'].'.php')) {
			return sprintf( __('There is a template file named single-%s.php in your theme directory (%s).', CCTM_TXTDOMAIN)
				, htmlspecialchars($data['post_type'])
				, get_stylesheet_directory());
		}
		*/

		return; // no errors
	}

	//------------------------------------------------------------------------------
	/**
	 * Everything when creating a new post type must be filtered here.
	 *
	 * Problems with:
	 *  hierarchical
	 *  rewrite_with_front
	 *
	 * This is janky... sorta doesn't work how it's supposed when combined with save_post_type_settings().
	 *
	 *
	 * @param mixed   $raw unsanitized $_POST data
	 * @return mixed filtered $_POST data (only white-listed are passed thru to output)
	 */
	public static function sanitize_post_type_def($raw) {
		$sanitized = array();

		unset($raw['custom_content_type_mgr_create_new_content_type_nonce']);
		unset($raw['custom_content_type_mgr_edit_content_type_nonce']);

		$raw = CCTM::striptags_deep(($raw));

		// WP always adds slashes: see http://kovshenin.com/archives/wordpress-and-magic-quotes/
		$raw = CCTM::stripslashes_deep(($raw));

		
		// Handle unchecked checkboxes
		if ( empty($raw['cctm_hierarchical_custom'])) {
			$sanitized['cctm_hierarchical_custom'] = '';
		}
		if ( empty($raw['cctm_hierarchical_includes_drafts'])) {
			$sanitized['cctm_hierarchical_includes_drafts'] = '';
		}
		if ( empty($raw['cctm_hierarchical_post_types'])) {
			$sanitized['cctm_hierarchical_post_types'] = array();
		}
		if ( !isset($raw['cctm_custom_columns_enabled'])) {
			$sanitized['cctm_custom_columns_enabled'] = 0;
		}
		if ( !isset($raw['cctm_enable_right_now'])) {
			$sanitized['cctm_enable_right_now'] = 0;
		}
		

		// This will be empty if no "supports" items are checked.
		if (!empty($raw['supports']) ) {
			$sanitized['supports'] = $raw['supports'];
			unset($raw['supports']);
		}
		else {
			$sanitized['supports'] = array();
		}

		if (!empty($raw['taxonomies']) ) {
			$sanitized['taxonomies'] = $raw['taxonomies'];
		}
		else {
			// do this so this will take precedence when you merge the existing array with the new one in the save_post_type_settings() function.
			$sanitized['taxonomies'] = array();
		}
		// You gotta unset arrays if you want the foreach thing below to work.
		unset($raw['taxonomies']);

		// Temporary thing... ????
		unset($sanitized['rewrite_slug']);

		// The main event
		// We grab everything except stuff that begins with '_', then override specific $keys as needed.
		foreach ($raw as $key => $value ) {
			if ( !preg_match('/^_.*/', $key) ) {
				$sanitized[$key] = CCTM::get_value($raw, $key);
			}
		}

		// Specific overrides below:
		$sanitized['description'] = strip_tags($raw['description']);
		
		// post_type is the only required field
		$sanitized['post_type'] = CCTM::get_value($raw, 'post_type');
		$sanitized['post_type'] = strtolower($sanitized['post_type']);
		$sanitized['post_type'] = preg_replace('/[^a-z0-9_\-]/', '_', $sanitized['post_type']);
		$sanitized['post_type'] = substr($sanitized['post_type'], 0, 20);

		// Our form passes integers and strings, but WP req's literal booleans,
		// so we do some type-casting here to ensure literal booleans.
		$sanitized['public']    = (bool) CCTM::get_value($raw, 'public');
		$sanitized['rewrite_with_front']     = (bool) CCTM::get_value($raw, 'rewrite_with_front');
		$sanitized['show_ui']     = (bool) CCTM::get_value($raw, 'show_ui');
		$sanitized['public']     = (bool) CCTM::get_value($raw, 'public');
		$sanitized['show_in_nav_menus']  = (bool) CCTM::get_value($raw, 'show_in_nav_menus');
		$sanitized['can_export']    = (bool) CCTM::get_value($raw, 'can_export');
		$sanitized['use_default_menu_icon'] = (bool) CCTM::get_value($raw, 'use_default_menu_icon');
		$sanitized['hierarchical']    = (bool) CCTM::get_value($raw, 'hierarchical');
		$sanitized['include_in_search']    = (bool) CCTM::get_value($raw, 'include_in_search');
		$sanitized['publicly_queryable']    = (bool) CCTM::get_value($raw, 'publicly_queryable');
		$sanitized['include_in_rss']    = (bool) CCTM::get_value($raw, 'include_in_rss');
		$sanitized['map_meta_cap']    = (bool) CCTM::get_value($raw, 'map_meta_cap');
		$sanitized['show_in_admin_bar']    = (bool) CCTM::get_value($raw, 'show_in_admin_bar');

		if ( empty($sanitized['has_archive']) ) {
			$sanitized['has_archive'] = false;
		}
		else {
			$sanitized['has_archive'] = true;
		}

		// *facepalm*... Special handling req'd here for menu_position because 0
		// is handled differently than a literal null.
		if ( (int) CCTM::get_value($raw, 'menu_position') ) {
			$sanitized['menu_position'] = (int) CCTM::get_value($raw, 'menu_position', null);
		}
		else {
			$sanitized['menu_position'] = null;
		}
		$sanitized['show_in_menu']    = CCTM::get_value($raw, 'show_in_menu');

		$sanitized['cctm_show_in_menu']    = CCTM::get_value($raw, 'cctm_show_in_menu');


		// menu_icon... the user will lose any custom Menu Icon URL if they save with this checked!
		// TODO: let this value persist.
		if ( $sanitized['use_default_menu_icon'] ) {
			unset($sanitized['menu_icon']); // === null;
		}

		if (empty($sanitized['query_var'])) {
			$sanitized['query_var'] = false;
		}

		// Cleaning up the labels
		if ( empty($sanitized['label']) ) {
			$sanitized['label'] = ucfirst($sanitized['post_type']);
		}
		if ( empty($sanitized['labels']['singular_name']) ) {
			$sanitized['labels']['singular_name'] = ucfirst($sanitized['post_type']);
		}
		if ( empty($sanitized['labels']['add_new']) ) {
			$sanitized['labels']['add_new'] = __('Add New');
		}
		if ( empty($sanitized['labels']['add_new_item']) ) {
			$sanitized['labels']['add_new_item'] = __('Add New') . ' ' .ucfirst($sanitized['post_type']);
		}
		if ( empty($sanitized['labels']['edit_item']) ) {
			$sanitized['labels']['edit_item'] = __('Edit'). ' ' .ucfirst($sanitized['post_type']);
		}
		if ( empty($sanitized['labels']['new_item']) ) {
			$sanitized['labels']['new_item'] = __('New'). ' ' .ucfirst($sanitized['post_type']);
		}
		if ( empty($sanitized['labels']['view_item']) ) {
			$sanitized['labels']['view_item'] = __('View'). ' ' .ucfirst($sanitized['post_type']);
		}
		if ( empty($sanitized['labels']['search_items']) ) {
			$sanitized['labels']['search_items'] = __('Search'). ' ' .ucfirst($sanitized['labels']['menu_name']);
		}
		if ( empty($sanitized['labels']['not_found']) ) {
			$sanitized['labels']['not_found'] = sprintf( __('No %s found', CCTM_TXTDOMAIN), strtolower($raw['labels']['menu_name']) );
		}
		if ( empty($sanitized['labels']['not_found_in_trash']) ) {
			$sanitized['labels']['not_found_in_trash'] = sprintf( __('No %s found in trash', CCTM_TXTDOMAIN), strtolower($raw['labels']['menu_name']) );
		}
		if ( empty($sanitized['labels']['parent_item_colon']) ) {
			$sanitized['labels']['parent_item_colon'] = __('Parent Page');
		}


		// Rewrites. TODO: make this work like the built-in post-type permalinks
		switch ($sanitized['permalink_action']) {
		case '/%postname%/':
			$sanitized['rewrite'] = true;
			break;
		case 'Custom':
			$sanitized['rewrite']['slug'] = $raw['rewrite_slug'];
			$sanitized['rewrite']['with_front'] = isset($raw['rewrite_with_front']) ? (bool) $raw['rewrite_with_front'] : false;
			break;
		case 'Off':
		default:
			$sanitized['rewrite'] = false;
		}
		
		return $sanitized;
	}


	//------------------------------------------------------------------------------
	/**
	 * this saves a serialized data structure (arrays of arrays) to the db
	 *
	 * @return
	 * @param mixed   $def associative array definition describing a single post-type.
	 */
	public static function save_post_type_settings($def) {

		$key = $def['post_type'];

		unset(CCTM::$data['post_type_defs'][$key]['original_post_type_name']);

		// Update existing settings if this post-type has already been added
		if ( isset(CCTM::$data['post_type_defs'][$key]) ) {
			CCTM::$data['post_type_defs'][$key] = array_merge(CCTM::$data['post_type_defs'][$key], $def);
		}
		// OR, create a new node in the data structure for our new post-type
		else {
			CCTM::$data['post_type_defs'][$key] = $def;
		}
		if (CCTM::$data['post_type_defs'][$key]['use_default_menu_icon']) {
			unset(CCTM::$data['post_type_defs'][$key]['menu_icon']);
		}

		update_option( CCTM::db_key, CCTM::$data );
		
        // http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=50
		// https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=540
        global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
}
/*EOF*/