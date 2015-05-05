<?php
/**
 * A class for programmatically creating posts with all their custom fields via 
 * a unified API.  It is similar to wp_insert_post() but with several important
 * differences:
 *
 *	1. It does not call any actions or filters when it is executed, so for better
 *		or for worse, there is no way for 3rd parties to intervene with this.
 *
 *	2. It automatically creates/updates/deletes all custom fields in the postmeta
 *		table without the need to have to use the update_post_meta() and related functions.
 *
 *	3. It does not check for user permissions. If you're running around in the PHP
 *		code, you have full run of the database anyhow. BEWARE!  If implementing this
 * 		in your own code/plugin, you should check the user permissions before executing functions
 * 		in this class.
 *
 *  4. Array values for custom fields (postmeta) are converted to JSON arrays.  
 *      This means that for each custom field, you will have exactly *one* row
 *      in the postmeta table.  This isn't how WP handles things natively, but there
 *      are advantages to doing things this way, and that makes this compatible with
 *      the CCTM, for which it was written.
 *
 * @pacakge SummarizePosts
 */
class SP_Post {

	private static $wp_posts_columns = array(
		'ID',
		'post_author',
		'post_date',
		'post_date_gmt',
		'post_content',
		'post_title',
		'post_excerpt',
		'post_status',
		'comment_status',
		'ping_status',
		'post_password',
		'post_name',
		'to_ping',
		'pinged',
		'post_modified',
		'post_modified_gmt',
		'post_content_filtered',
		'post_parent',
		'guid',
		'menu_order',
		'post_type',
		'post_mime_type',
		'comment_count'
	);

	/**
	 * Assoc. array that maps a column name to a validator function
	 */
	public $validators = array(
		'ID' 				=> 'int',
		'post_title'		=> 'text',
		'post_content'		=> 'content',
		'post_author'		=> 'int',
		'post_date'			=> 'date',
		'post_date_gmt'		=> 'date',
		'post_modified' 	=> 'date',
		'post_modified_gmt' => 'date',
		'post_parent' 		=> 'int',
		'menu_order' 		=> 'int',
		'comment_count'		=> 'int',
	);

	public $errors = array();
	
	public $Q; // where GetPostQuery gets instantiated.
	
	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public function __construct() {

	}
	
	//------------------------------------------------------------------------------
	//! Private Functions
	//------------------------------------------------------------------------------
	/**
	 * Content protection (allows html tags). See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=454
	 * @param	string	$val
	 * @return	string
	 */
	private function _content($val) {
		return wp_kses_post($val, array());
	}
		
	//------------------------------------------------------------------------------
	/**
	 * Convert input to datestamp
	 * @param	string	$val
	 * @param	string
	 */
	private function _date($val) {
		return date('Y-m-d H:i:s', strtotime($val));
	}
	
	//------------------------------------------------------------------------------
	/**
	 * @param	string	$val
	 * @return	integer
	 */
	private function _int($val) {
		return (int) $val;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Tests whether a string is valid for use as a MySQL column name.  This isn't 
	 * 100% accurate because the postmeta virtual columns can be more flexible, but
	 * generally, we want to enforce vanilla column names in case they end up being
	 * used as object properties.
	 * @param	string
	 * @return	boolean
	 */
	private function _is_valid_column_name($str) {
		if (preg_match('/[^a-zA-Z0-9\/\-\_]/', $str)) {
			return false;
		}
		else {
			return true;
		}
	}
		
	//------------------------------------------------------------------------------
	/**
	 * Used to override filtering. Use carefully.
	 *
	 * @param	string	$val
	 * @return	string
	 */
	private function _none($val) {
		return $val;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Basic text protection.  This is stricter than "content" protection: content
	 * allows some HTML.
	 * @param	string	$val
	 * @return	string
	 */
	private function _text($val) {
		return wp_kses($val, array());
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Run the arguments through the various validators defined
	 */
	private function _sanitize($args) {
        $sanitized = array();
		foreach ($args as $k => $v) {
            // Validators are only set for valid column names
			// TODO: check for custom-field validators
			if (isset($this->validators[$k])) {
				$func_name = '_'.$this->validators[$k];
				$sanitized[$k] = $this->$func_name($v);
			}
			elseif($this->_is_valid_column_name($k)) {
				$sanitized[$k] = $this->_text($v);
			}
			else {
				$this->errors[] = 'Invalid column name: ' . $k;
			}
		}
		return $sanitized;
	}
	
	
	//------------------------------------------------------------------------------
	//! Public Functions
	//------------------------------------------------------------------------------
	/**
	 * Format any errors in an unordered list, or returns a message saying there were no errors.
	 *
	 * @return string message detailing errors.
	 */
	public function debug() {

		$output = '';
		
		$errors = $this->errors;
		
		if ($errors) {
			$items = '';
			foreach ($errors as $e) {
				$items .= '<li>'.$e.'</li>' ."\n";
			}
			$output = '<ul>'."\n".$items.'</ul>'."\n";
		}

        if ($this->Q) {
           $output .= $this->Q->debug(); 
        }
	   
        if (empty($output)) {
			$output = __('There were no errors.', CCTM_TXTDOMAIN);
        }
		
		return sprintf('<h2>%s</h2><div class="summarize-posts-errors">%s</div>'
			, __('Errors', CCTM_TXTDOMAIN)
			, $output);
	}
	
	/**
	 * Deletes a post, its custom fields, and any revisions of that post. 
	 * @param	integer	$post_id
	 */
	public function delete($post_id) {
		global $wpdb;
		
		$post_id = (int) $post_id;
		
		// Delete the custom fields
		$query = $wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE post_id = %s"
			, $post_id);
		$wpdb->query($query);		

		// Delete taxonomy refs
		$query = $wpdb->prepare("DELETE FROM {$wpdb->term_relationships} WHERE object_id = %s",$post_id);
		$wpdb->query($query);

		
		// Delete any revisions
		$query = $wpdb->prepare("DELETE a FROM {$wpdb->posts} a INNER JOIN {$wpdb->posts} b ON a.post_parent=b.ID WHERE a.post_type='revision' AND b.ID=%s"
			, $post_id);
		$wpdb->query($query);
				
		// Delete the posts
		$query = $wpdb->prepare("DELETE FROM {$wpdb->posts} WHERE ID=%s;"
			, $post_id);
		$wpdb->query($query);		
		
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Convenience function that ties into GetPostsQuery to retrieve a single post. 
	 * You can provide a simple post_id to retrieve a single post, or you can provide
	 * complex search criteria a la GetPostsQuery::get_posts(), but this function 
	 * will only return a single result.
	 *
	 * @param	mixed	$args	any valid search params for GetPostsQuery, or integer post id (for retrieving 1 post)
	 * @return	mixed -- a single associative array if an integer was supplied, or false on no results.
	 */
	public function get($args) {
		$this->Q = new GetPostsQuery();
		if (is_array($args)) {
			$args['limit'] = 1; // for database efficiency
			$posts = $this->Q->get_posts($args);
			if (!empty($posts)) {
				return $posts[0];
			}
			else {
				return false;
			}
		}
		else {
			$post_id = (int) $args;
			$post = $this->Q->get_post($post_id);
			if (!empty($post)) {
				return $post;
			}
			else {
				return false;
			}
		}
	}
	
	//------------------------------------------------------------------------------
	/**
	 * INSERT INTO `wp_posts` (ID,post_title) VALUES ('666','satan');
	 * If you got no values, the minimal insertion would be this:
	 * INSERT INTO `wp_posts` (ID) VALUES (NULL);
	 *
	 * @param	array	$args
	 * @return	integer	post_id or boolean false
	 */
	public function insert($args) {
		
		global $wpdb;
		
		unset($args['ID']); // just in case
		if(!$args = $this->_sanitize($args)) {
            return false;
		}

		// Get the primary columns
		$posts_args = array();
		$postmeta_args = array();
		foreach ($args as $k => $v) {
			if (in_array($k, self::$wp_posts_columns)) {
				$posts_args[$k] = $v;
			}
			else {
				$postmeta_args[$k] = $v;	
			}
		}

		if ($wpdb->insert($wpdb->posts, $posts_args) == false) {
			$this->errors[] = "Error inserting row into {$wpdb->posts}";
			return false;
		}
		
		$post_id = $wpdb->insert_id;
		
		if (!empty($postmeta_args)) {
			$query = "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES ";
			$meta_rows = array();
			foreach ($postmeta_args as $k => $v) {
				if (is_array($v)) {
					$v = json_encode($v);
				}
				else {
				    $v = stripslashes($v);
				}
                $meta_rows[] = $wpdb->prepare('(%d, %s, %s)', $post_id, $k, $v);
			}
			
			$meta_str = implode(', ', $meta_rows);
			
			$query = "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES " . $meta_str;
			$wpdb->query($query);
		}
		
		return $post_id;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Intelligently switch between insert/update
	 *
	 * @return	integer post id on success, false on failure
	 */
	public function save($args) {
		if (isset($args['ID']) && $args['ID'] != 0) {
			$post = get_post($args['ID'], ARRAY_A);
			if (!empty($post)) {
				return $this->update($args,$args['ID']);
			}
		}
		
		return $this->insert($args);
	}

	//------------------------------------------------------------------------------
	/**
	 * Sets a validator option: useful if you want to customize how data is filtered
	 * before you save it.
	 *
	 * @param string $field : name of the field
	 * @param stirng $validator : none, int, date, text, content
	 */
	public function set_validator($field, $validator) {
		$this->validators[$field] = $validator;
	}

	//------------------------------------------------------------------------------
	/**
	 * If you leave the 3rd arg as false, a "line-item" update is performed. Custom
	 * fields are left alone unless they are specified in the $args.
	 * If you set $overwrite to true, however, any custom fields not included in the 
	 * $args will be purged from the database.
	 *
	 * @param	array	$args in column => value pairs
	 * @param	integer	$post_id the post you want to update
	 * @param	boolean	$overwrite (optional). Default: false.
	 * @return 	mixed	integer $post_id on success, boolean false on fail
	 */
	public function update($args, $raw_post_id, $overwrite=false) {
		
		$post_id = (int) $raw_post_id;
		global $wpdb;
		
		if (!$post_id) {
			$this->errors[] = __('Update requires post ID.', CCTM_TXTDOMAIN);
			return false;
		}
		if ($post_id != $raw_post_id) {
			$this->errors[] = __('Invalid value for post ID. Cannot update post.', CCTM_TXTDOMAIN);
			return false;	
		}	
	
		$args = $this->_sanitize($args);

		// Get the primary columns
		$posts_args = array();
		$postmeta_args = array();
		foreach ($args as $k => $v) {
			if (in_array($k, self::$wp_posts_columns)) {
				$posts_args[$k] = $v;
			}
			else {
				$postmeta_args[$k] = $v;	
			}
		}

		// Main fields
		$wpdb->update( $wpdb->posts, $posts_args, array('ID' => $post_id));

		// Custom fields
		if ($overwrite) {
			$query = $wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE post_id = %s", $post_id);
			$wpdb->query($query);
		}
		
		foreach ($postmeta_args as $k => $v) {
			if (is_array($v)) {
				$v = json_encode($v);
			}
            // We  can't rely in what $wpdb->update returns to know whether or not the row already exists, so we must first 
            // try to select it.
            $find_meta_sql = $wpdb->prepare("SELECT meta_id FROM {$wpdb->postmeta} WHERE post_id = %s AND meta_key=%s"
                , $post_id,$k);
            $existing_meta = $wpdb->get_col($find_meta_sql);
            // Update existing row
            if ($existing_meta) {
    			if ($wpdb->update($wpdb->postmeta, array('meta_value' => $v), array('post_id' => $post_id, 'meta_key' => $k)) === false ) {
                    $this->errors[] = "Error creating row in {$wpdb->postmeta} where post_id = $post_id and meta_key $k";
                    return false;
    			 }
            }
            // New row
            else {
                if ($wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => $k, 'meta_value'=>$v)) === false) {
                    $this->errors[] = "Error inserting row into {$wpdb->postmeta} where post_id = $post_id and meta_key $k ";
                    return false;
                }
            }
		}
		
		return $post_id; // successful if we made it here.
		
	}
}


/*EOF*/