<?php
/**
These are functions in the main namespace, primarily reserved for use in 
theme files.

See http://code.google.com/p/wordpress-custom-content-type-manager/wiki/TemplateFunctions
for the official documentation.
*/

//------------------------------------------------------------------------------
/**
 * SYNOPSIS: Used inside theme files, e.g. single.php or single-my_post_type.php
 * where you need to print out the value of a specific custom field. This can also
 * get called via the [custom_field] shortcode.  If identical instances of the same
 * call, the output will be from the request cache.
 * 
 * WordPress allows for multiple rows in wp_postmeta to share the same meta_key for 
 * a single post; the CCTM plugin expects all meta_key's for a given post_id to be 
 * unique.  To deal with the possibility that the user has created multiple custom 
 * fields that share the same name for a single post (e.g. created manually with the 
 * CCTM plugin disabled), this prints the 1st instance of the meta_key identified by 
 * $fieldname associated with the current post. See get_post_meta() for more details.
 *
 * See also 	
 * http://codex.wordpress.org/Function_Reference/get_post_custom_values
 *
 * @param	string the name of the custom field (exists in wp_postmeta).
 * 		Optionally this string can be in the format of 'fieldname:output_filter'
 * @param	mixed	can be used to specify additional arguments
 * @return	mixed	The contents of the custom field, processed through output filters
 */
function get_custom_field($raw_fieldname, $options=null) {

	global $post;
	
	// Shortcodes can override which post they're retrieving data for
	if (!empty(CCTM::$post_id)) {
		$post_id = CCTM::$post_id;
	}
	elseif (is_object($post) && isset($post->ID)) {
		$post_id = $post->ID;
	}
	else {
	   return '$post not defined.';
	}
	
	$options_array = func_get_args();

	// Request cache: this helps speed up cases where there are identical instances of get_custom_field()
	// Get the cache key
	$cache_key = serialize($options_array) . $post_id;
	if (isset(CCTM::$cache[$cache_key])) {
		return CCTM::$cache[$cache_key]; // done!  Output comes from cache!
	}

	// Extract any output filters.
	$input_array = explode(':',$raw_fieldname);	
	$fieldname = array_shift($input_array);
	
	// We need the custom field definition for 2 reasons:
	// 1. To find the default Output Filter
	// 2. To find any default value (if the field is not defined)
	if ( !isset(CCTM::$data['custom_field_defs'][$fieldname]) ) {
		// return get_post_meta($post->ID, $fieldname, true); // ???
		return sprintf( __('The %s field is not defined as a custom field.', CCTM_TXTDOMAIN), "<code>$fieldname</code>" );
	}
	
	
	// Get default output filter
	if (empty($input_array)){
		if (isset(CCTM::$data['custom_field_defs'][$fieldname]['output_filter']) 
			&& !empty(CCTM::$data['custom_field_defs'][$fieldname]['output_filter'])) {
			$input_array[] = CCTM::$data['custom_field_defs'][$fieldname]['output_filter'];
		}
	}
	// Raw value from the db
	$value = get_post_meta($post_id, $fieldname, true);

	// Default value? See http://wordpress.org/support/topic/default-value-behaviour?replies=7
	//if ( empty($value) && isset(CCTM::$data['custom_field_defs'][$fieldname]['default_value'])) {
	//	$value = CCTM::$data['custom_field_defs'][$fieldname]['default_value'];
	//}

	// Pass thru Output Filters
	$i = 1; // <-- skip 0 b/c that's the $raw_fieldname in the $options_array
	foreach($input_array as $outputfilter) {

		if (isset($options_array[$i])) {
			$options = $options_array[$i];
		}
		else {
			$options = null;
		}

		$value = CCTM::filter($value, $outputfilter, $options);

		$i++;
	}
	// Store in the request cache
	CCTM::$cache[$cache_key] = $value;
	
	return $value;	
}


//------------------------------------------------------------------------------
/**
* Gets info about a custom field's definition (i.e. the meta info about the
* field). Returns error messages if no data found.  If you supply a 2nd 
*
* Sample usage: <?php print get_custom_field_meta('my_dropdown','label'); ?>
*
* @param	string	$fieldname	The name of the custom field
* @param	string	$item		(optional) The name of the definition item that you want
* @return	mixed	Usually a string, but some items are arrays (e.g. options)
*/
function get_custom_field_meta($fieldname, $item=null) {
	$data = get_option( CCTM::db_key, array() );
	
	if (isset($data['custom_field_defs'][$fieldname])) {
		if ($item && isset($data['custom_field_defs'][$fieldname][$item])) {
			return $data['custom_field_defs'][$fieldname][$item];
		}
		else {
			return $data['custom_field_defs'][$fieldname];
		}
	}
	else {
		return sprintf( __('Invalid field name: %s', CCTM_TXTDOMAIN), $fieldname );
	}
}

//------------------------------------------------------------------------------
/**
* Gets the custom image referenced by the custom field $fieldname. 
* Relies on the WordPress wp_get_attachment_image() function.
*
* @param	string	$fieldname name of the custom field
* @return	string	an HTML img element or empty string on failure.
*/
function get_custom_image($fieldname) {
	$id = get_custom_field($fieldname.':raw');
	return wp_get_attachment_image($id, 'full');
}



//------------------------------------------------------------------------------
/**
 * Get posts that link to this post via a relation field.
 *
 * @param	array	(optional) post_types you want to display. Default behavior 
 *					calculates which post types contain relation fields
 * @param	integer	the post id. If not set, we assume it's the current post id.
 * @return array post IDs
 */
function get_incoming_links($post_types_filter=array(), $post_id=null) {
	
	require_once(CCTM_PATH.'/includes/SummarizePosts.php');
	require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
	
	global $post;
	global $wpdb;
	
	
	// We need fields that point to THIS post
	if (empty($post_id)) {
		$post_id = $post->ID;
	}
	

	// Get post-types containing relation fields
	// First: gather up the relation fields
	$relation_fields = array();
	$relation_fields_str = '';
	$post_types = array();
	$post_types_str = '';

	if (isset(CCTM::$data['custom_field_defs']) && is_array(CCTM::$data['custom_field_defs']) ) {
		foreach (CCTM::$data['custom_field_defs'] as $name => $def) {
			if ($def['type'] == 'relation') {
				$relation_fields[] = $name; 
			}
		}
	}

	if (empty($relation_fields)) {
		return array();
	}

	// Which post types contain the relations?
	if (isset(CCTM::$data['post_type_defs']) && is_array(CCTM::$data['post_type_defs']) ) {
		foreach (CCTM::$data['post_type_defs'] as $post_type => $def) {
			if (!isset($def['is_active']) || !$def['is_active']) {
				continue; // only track active post types.
			}
			
			if (isset($def['custom_fields']) && is_array($def['custom_fields'])) {
				foreach($relation_fields as $field) {
					$custom_fields = $def['custom_fields'];
					if (in_array($field, $custom_fields)) {
						$post_types[] = $post_type;
						continue 2; // skip to the next post type
					}
				}
			}
		}
	}

	if (empty($post_types)) {
		return array();
	}
	
	// Filter post-types
	if (!empty($post_types_filter)) {
		$new_post_types = array();
		foreach ($post_types_filter as $ptf) {
			if (in_array($ptf, $post_types)) {
				$new_post_types[] = $ptf;
			}
		}
		$post_types = $new_post_types;
	}
	
	foreach ($relation_fields as $i => $f) {
		$relation_fields[$i] = $wpdb->prepare('%s', $f); // quote each entry
	}
	foreach ($post_types as $i => $pt) {
		$post_types[$i] = $wpdb->prepare('%s', $pt); // quote each entry
	}

	$relation_fields_str = implode(',', $relation_fields);
	$post_types_str = implode(',', $post_types);
	
	// normal fields		
	$sql = $wpdb->prepare("SELECT {$wpdb->postmeta}.post_id 
		FROM {$wpdb->posts} JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
		WHERE {$wpdb->postmeta}.meta_value=%s
		AND {$wpdb->postmeta}.meta_key IN ($relation_fields_str)
		AND {$wpdb->posts}.post_type IN ($post_types_str)"
	, $post_id);

	$single_results = $wpdb->get_results( $sql, ARRAY_A );
			

	// relations
	$sql = $wpdb->prepare("SELECT {$wpdb->postmeta}.post_id 
		FROM {$wpdb->posts} JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
		WHERE {$wpdb->postmeta}.meta_value LIKE %s
		AND {$wpdb->postmeta}.meta_key IN ($relation_fields_str)
		AND {$wpdb->posts}.post_type IN ($post_types_str)"
	, '%"'.$post_id.'"%', $post_id);

	$multi_results = $wpdb->get_results( $sql, ARRAY_A );		

	// Harvest the IDs
	$mixed = array();
	foreach($single_results as $r) {
		$mixed[] = $r['post_id'];
	}
	foreach($multi_results as $r) {
		$mixed[] = $r['post_id'];
	}
	
	return array_unique($mixed);
}

//------------------------------------------------------------------------------
/**
 * Retrieves a complete post object, including all meta fields. It avoids the 
 * standard WP functions get_post() and get_post_custom() because they encountered
 * some weird issues with conflicting global variables (?):
 * http://codex.wordpress.org/Function_Reference/get_post
 * http://codex.wordpress.org/Function_Reference/get_post_custom
 *
 * Returned is a post array that contains a key for each field and custom field, e.g.
 * 
 * print $post['post_title'];
 * print $post['my_custom_field']; // not $post['my_custom_fields'][0];
 * 
 * and if the custom field *is* a list of items, then attach it as such.
 * 
 * @param	integer	$id is valid ID of a post (regardless of post_type).
 * @return	array	associative array of post with all attributes, including custom fields.
 */
function get_post_complete($id) {
	$Q = new GetPostsQuery();
	return $Q->get_post($id);
}

//------------------------------------------------------------------------------
/**
 * Returns an array of post "complete" objects (including all custom fields)
 * where the custom fieldname = $fieldname and the value of that field is $value.
 * This is used to find a bunch of related posts in the same way you would with 
 * a taxonomy, but this uses custom field values instead of taxonomical labels.
 * 
 * INPUT: 
 * 	$fieldname (str) name of the custom field
 * 	$value (str) the value that you are searching for.
 * 
 * OUTPUT:
 * 	array of post objects (complete post objects, with all attributes).
 * 
 * USAGE:
 * 	One example:
 * 	$posts = get_posts_sharing_custom_field_value('genre', 'comedy');
 * 	
 * 	foreach ($posts as $p) {
 * 		print $p->post_title;
 * 	}
 * 
 * This is a hefty, db-intensive function... (bummer).
 */
function get_posts_sharing_custom_field_value($fieldname, $value) {
	global $wpdb;
	$query = "SELECT DISTINCT {$wpdb->posts}.ID 
		FROM {$wpdb->posts} JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id  
		WHERE 
		{$wpdb->posts}.post_status = 'publish'
		AND {$wpdb->postmeta}.meta_key=%s AND {$wpdb->postmeta}.meta_value=%s";
	$results = $wpdb->get_results( $wpdb->prepare( $query, $fieldname, $value ), OBJECT );
	
	$completes = array();
	foreach ( $results as $p )
	{
		$completes[] = get_post_complete($p->ID);
	}
	return $completes;
}


//------------------------------------------------------------------------------
/**
 * A relation field stores a post ID, and that ID identifies another post.  So given 
 * a fieldname, this returns the complete post object for that was referenced by
 * the custom field.  You can see it's a wrapper function which relies on 
 * get_post_complete() and get_custom_field().
 * INPUT: 
 * 	$fieldname (str) name of a custom field
 * OUTPUT:
 * 	post object
 */
function get_relation($fieldname) {
	return get_post_complete( get_custom_field($fieldname.':raw') );
}

//------------------------------------------------------------------------------
/**
 * Given a specific custom field name ($fieldname), return an array of all unique
 * values contained in this field by *any* published posts which use a custom field 
 * of that name, regardless of post_type, and regardless of whether or not the custom 
 * field is defined as a "standardized" custom field. 
 * 
 * This filters out empty values ('' or null). 
 *
 * USAGE:
 * Imagine a custom post_type that profiles you and your friends. There is a custom 
 * field that defines your favorite cartoon named 'favorite_cartoon':
 * 
 * 	$array = get_unique_values_this_custom_field('favorite_cartoon');
 * 	
 * 	print_r($array);
 * 		Array ( 'Family Guy', 'South Park', 'The Simpsons' );
 * 
 * INPUT:
 * @param	string	$fieldname	name of a custom field
 * @param	string	$order	specify the order of the results returned, either 'ASC' (default) or 'DESC'
 * @return	array 	unique values.
 * 
 */
function get_unique_values_this_custom_field($fieldname, $order='ASC') {
	global $wpdb;

	$order = strtoupper($order);
	// Sanitize
	if ($order != 'ASC' && $order != 'DESC') {
		$order = 'ASC';  // back to default.
	}
	$query = "SELECT DISTINCT {$wpdb->postmeta}.meta_value 
		FROM {$wpdb->postmeta} JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
		WHERE {$wpdb->postmeta}.meta_key=%s 
		AND {$wpdb->postmeta}.meta_value !=''
		AND {$wpdb->posts}.post_status = 'publish'
		ORDER BY {$wpdb->postmeta}.meta_value $order";

	$sql = $wpdb->prepare($query, $fieldname);
	//print '<textarea>'.$sql.'</textarea>';
	$results = $wpdb->get_results( $sql, ARRAY_N );	
	//print_r($results); exit;
	// Repackage
	$uniques = array();
	foreach ($results as $r )
	{
		$uniques[] = $r[0];
	}

	return array_unique($uniques);
}

//------------------------------------------------------------------------------
/**
 * SYNOPSIS: Used inside theme files, e.g. single.php or single-my_post_type.php
 * where you need to print out the value of a specific custom field.
 * 
 * This prints the 1st instance of the meta_key identified by $fieldname 
 * associated with the current post. See get_post_meta() for more details.
 * 
 * INPUT: 
 * 	$fieldname (str) the name of the custom field as defined inside the 
 * 		Manage Custom Fields area for a particular content type.
 * OUTPUT:
 * 	The contents of that custom field for the current post.
 */
function print_custom_field($fieldname, $extra=null) {
	print get_custom_field($fieldname, $extra);
}

//------------------------------------------------------------------------------
/**
 * Convenience function to print the result of get_custom_field_meta().  See
 * get_custom_field_meta.
 */
function print_custom_field_meta($fieldname, $item, $post_type=null) {
	print call_user_func_array('get_custom_field_meta', func_get_args());
}


//------------------------------------------------------------------------------
/**
 * Print posts that link to this post via a relation field.
 * @param	string	$tpl
 * @return void -- this actually prints data.
 */
function print_incoming_links($tpl=null) {
	if (empty($tpl)) {
		$tpl = '<span><a href="[+permalink+]">[+post_title+] ([+ID+])</a></span> &nbsp;';
	}
	
	$Q = new GetPostsQuery();
	$args = array();
	
	$args['include'] = get_incoming_links();
//	$args['post_status'] = 'draft,publish,inherit';
	
	$results = $Q->get_posts($args);
	
	$output = '';
	foreach ($results as $r) {
		$output .= CCTM::parse($tpl, $r);
	}

	print $output;

}

/*EOF*/