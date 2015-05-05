<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Scan the database and determine what post_types are already in existence.  
The user would have to eyeball them to determine descriptions etc.
------------------------------------------------------------------------------*/

/*EOF*/