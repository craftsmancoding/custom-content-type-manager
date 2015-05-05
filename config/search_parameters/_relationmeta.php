<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
/*------------------------------------------------------------------------------
Search Parameters Configuration

This file determines which elements will be shown on the forms that appear in a 
lightbox when a user is defining a relation field and is setting the search 
criteria.  If you need to "unlock" more search criteria, you can push the 
necessary form terms onto the CCTM::$search_by array. 

For example, if you need your users to select draft posts when they select a
related post (instead of just published posts), you can make this search option
appear when you define the field's search parameters by adding the 'post_status'
parameter to the CCTM::$search_by array, e.g.

CCTM::$search_by[] = 'post_status';

This file should ONLY populate the CCTM::$search_by array, e.g.

	CCTM::$search_by[] = 'post_type';
	CCTM::$search_by[] = 'taxonomy';
	// ... etc... 
	

The options available as "search_by" parameters include any option available to the 
GetPostsQuery::get_posts() function:

http://code.google.com/p/wordpress-summarize-posts/wiki/get_posts

DO NOT OVERWRITE THIS FILE DIRECTLY!  Instead, create a copy of this file inside
wp-content/uploads/cctm/search_parameters/ -- this ensures that your
custom modications are preserved in a place that will not be overwritten by the 
WordPress update process.
------------------------------------------------------------------------------*/

CCTM::$search_by[] = 'post_type';
CCTM::$search_by[] = 'post_status';
CCTM::$search_by[] = 'taxonomy';
CCTM::$search_by[] = 'taxonomy_term';
CCTM::$search_by[] = 'post_parent';
CCTM::$search_by[] = 'meta_key';
CCTM::$search_by[] = 'meta_value';


/*EOF*/