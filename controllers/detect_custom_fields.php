<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Detect existing custom fields in the database and use them to identify 
possible field types.
------------------------------------------------------------------------------*/

/*EOF*/