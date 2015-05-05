<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
 * The CCTM gives precedence to user-created files inside of uploads/cctm/ over
 * built-in files inside the plugin's directory. This page highlights which files
 * have been overridden by local customizations.
 
CCTM::$data = Array( 
 
 [cache] => Array
        (
            [helper_classes] => Array
                (
                    [filters] => Array
                        (
                            [datef] => /Users/everett2/Sites/cctm/html/wp-content/plugins/custom-content-type-manager/filters/datef.php
                            [default] => /Users/everett2/Sites/cctm/html/wp-content/plugins/custom-content-type-manager/filters/default 
------------------------------------------------------------------------------*/


$data 				= array();
$data['page_title']	= __('Show Sources', CCTM_TXTDOMAIN);
$data['help']		= 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/';
$data['menu'] 		= ''; 
$data['msg']		= ''; // CCTM::format_msg('Test...');
$data['action_name'] = '';
$data['nonce_name'] = '';
$data['submit']   = '';

$data['tpls'] = '';
$data['configs'] = '';
$data['fields']   = '';
$data['filters'] = '';


$data['content'] = CCTM::load_view('sources.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/