<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
/*------------------------------------------------------------------------------
Post Selector Configuration: for Post Content Widget

This file sets out configuration details for the "Post Selector" forms
generated when you create or edit a post that uses an image, media, or relation
field.  

The available search parameters here kick in only if no where no explicit 
Search Paramters have been configured (see the Search Parameters configuration),
so you could rely solely on the Search Parameters to achieve the search filters
you want, or conversely, you could skip the Search Parameters entirely and 
enforce your default search criteria here.  Having 2 places where the filters
can be adjusted allows you to open up certain criteria for user selection.

This file should ONLY populate the CCTM::$post_selector array, and the valid keys
should be valid arguments to the GetPostsQuery::get_posts() function:

	CCTM::$post_selector['post_type'] = 'attachment';
	CCTM::$post_selector['post_mime_type'] = 'image';
	// ... etc... 

See http://code.google.com/p/wordpress-summarize-posts/wiki/get_posts e.g.

DO NOT OVERWRITE THIS FILE DIRECTLY!  Instead, create a copy of this file inside
wp-content/uploads/cctm/post_selector/ -- this ensures that your
custom modications are preserved in a place that will not be overwritten by the 
WordPress update process.
------------------------------------------------------------------------------*/

CCTM::$post_selector['search_columns'] = array('post_title', 'post_content');
CCTM::$post_selector['post_status'] = array('publish','inherit');
CCTM::$post_selector['orderby'] = 'ID';
CCTM::$post_selector['order'] = 'DESC';
CCTM::$post_selector['limit'] = 10;
CCTM::$post_selector['paginate'] = 1;

CCTM::$search_by = array();
CCTM::$search_by[] = 'post_type';
CCTM::$search_by[] = 'post_status';
CCTM::$search_by[] = 'taxonomy';
CCTM::$search_by[] = 'taxonomy_term';
CCTM::$search_by[] = 'post_parent';
CCTM::$search_by[] = 'meta_key';
CCTM::$search_by[] = 'meta_value';

/*EOF*/