<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Themes Page
------------------------------------------------------------------------------*/
$data 				= array();
$data['page_title']	= __('Themes', CCTM_TXTDOMAIN);
$data['menu'] 		='';
$data['msg']		= '';
$data['action_name']  = 'custom_content_type_mgr_theme';
$data['nonce_name']  = 'custom_content_type_mgr_theme';
$data['submit']   = __('Save', CCTM_TXTDOMAIN);



// If properly submitted, Proceed with deleting the post type
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	$data['msg'] = 'Updating...';

}

$data['content'] = 'Theme stuff goes here......';

$data['content'] = CCTM::load_view('basic_form.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/