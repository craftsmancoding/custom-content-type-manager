<?php
/*------------------------------------------------------------------------------
This controller takes serialized arguments meant for the GetPostsQuery class 
and formats them to be easily readible.  This helps users know what search 
criteria they have defined for a widget or other search parameter.
------------------------------------------------------------------------------*/
if (!defined('CCTM_PATH')) exit('No direct script access allowed');

require_once(CCTM_PATH.'/includes/GetPostsQuery.php');

$search_parameters_str = '';
if (isset($_POST['search_parameters'])) {
	$search_parameters_str = $_POST['search_parameters'];
}

$args = array();
parse_str($search_parameters_str, $args);

$Q = new GetPostsQuery($args);

print $Q->get_args();

/*EOF*/