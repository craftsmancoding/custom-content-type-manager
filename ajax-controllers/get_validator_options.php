<?php
/*------------------------------------------------------------------------------
Load up the validator and return the validator's options.

@param	string	validator
------------------------------------------------------------------------------*/
$validator = CCTM::get_value($_POST,'validator');

$V = CCTM::load_object($validator,'validators');

if ($V){
	print $V->draw_options();
}
else {
	 print '<pre>'.__('Error loading validator.', CCTM_TXTDOMAIN).'</pre>';
}

/*EOF*/