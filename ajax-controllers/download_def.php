<?php
//if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
//if (!current_user_can('manage_options')) exit('You do not have permission to do that.');
/*------------------------------------------------------------------------------
Standalone controller to cough up a download.
------------------------------------------------------------------------------*/
require_once('../../../../wp-load.php');
require_once('../includes/constants.php');
require_once('../includes/CCTM.php');
require_once(CCTM_PATH.'/includes/CCTM_ImportExport.php');

CCTM_ImportExport::export_to_desktop();

/*EOF*/