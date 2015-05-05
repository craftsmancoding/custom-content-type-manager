<?php
/*------------------------------------------------------------------------------
This controller is meant to take some serialized Summarize Posts parameters
(e.g. ones that define a search for an image, relation, or media field) and 
convert them to human-readable arguments so a user will see what filters
are defined for a relation field.

@param string	$_POST['search_parameters']
------------------------------------------------------------------------------*/
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');

$search_parameters_str = '';
if (isset($_POST['search_parameters'])) {
	$search_parameters_str = $_POST['search_parameters'];
}
//print '<pre>'.$search_parameters_str. '</pre>'; return;
$existing_values = array();
parse_str($search_parameters_str, $existing_values);

$Q = new GetPostsQuery($existing_values);

print $Q->get_args();

return;