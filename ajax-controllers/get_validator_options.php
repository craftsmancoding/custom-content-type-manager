<?php
/*------------------------------------------------------------------------------
Load up the validator and return the validator's options.

@param	string	validator
------------------------------------------------------------------------------*/

$classname = 'CCTM\\Validators\\'.CCTM::get_value($_POST,'validator');

try {
    $V = new $classname();
	print $V->draw_options();
}
catch(Exception $e) {
	 print '<pre>'.__('Error loading validator', CCTM_TXTDOMAIN).' '.$validator.'</pre>';
}

/*EOF*/