<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
/*------------------------------------------------------------------------------
Shown after a user clicks "Report a Bug": this is intended to make bug reporting
more effective by displaying summarized info to the user so they can copy
and paste it into their bug report.
------------------------------------------------------------------------------*/
$data 				= array();
$data['page_title']	= __('Report a Bug', CCTM_TXTDOMAIN);
$data['menu'] 		= sprintf('<a href="http://code.google.com/p/wordpress-custom-content-type-manager/issues/list" class="button" target="_blank">%s</a>'
	, __('Launch Bug Tracker', CCTM_TXTDOMAIN) );
$data['msg']		= '';
$data['content'] = CCTM::load_view('bug_report.php');
print CCTM::load_view('templates/default.php', $data);
/*EOF*/