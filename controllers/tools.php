<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Tools Page: displays available tools
------------------------------------------------------------------------------*/
$data 				= array();
$data['page_title']	= __('Tools', CCTM_TXTDOMAIN);
$data['help']		= 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Tools';
$data['menu'] 		= self::get_flash();
$data['msg']		= '';
$data['action_name']  = 'custom_content_type_mgr_theme';
$data['nonce_name']  = 'custom_content_type_mgr_theme';
$data['submit']   = __('Save', CCTM_TXTDOMAIN);


$data['content'] = CCTM::load_view('tools.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/