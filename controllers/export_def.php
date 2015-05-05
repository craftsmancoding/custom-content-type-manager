<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Export a content type definition to a .json file
------------------------------------------------------------------------------*/
$data 				= array();
$data['page_title']	= __('Export Definition', CCTM_TXTDOMAIN);
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Export';
$data['menu'] 		= $data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_tools&a=tools" title="%s" class="button">%s</a>', __('Back'), __('Back')) . ' ' .
	sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_tools&a=import_def" title="%s" class="button">%s</a>',__('Import'), __('Import'));;
$data['msg']		= '';
$data['action_name']  = 'custom_content_type_mgr_export';
$data['nonce_name']  = 'custom_content_type_mgr_export';
$data['submit']   = __('Save', CCTM_TXTDOMAIN);
$data['content'] = '';


// If properly submitted, Proceed with saving settings and exporting def.
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	
	require_once(CCTM_PATH . '/includes/CCTM_ImportExport.php');
	
	$sanitized = CCTM_ImportExport::sanitize_export_params($_POST, $data['nonce_name']);
	
	// Any errors?
	if ( !empty(CCTM::$errors) ) {
		$data['msg'] = CCTM::format_errors();
	}
	// Download to desktop
	elseif ($_POST['export_type'] == 'download') {
		$nonce = wp_create_nonce('cctm_download_definition');
		
		$save_me = CCTM_ImportExport::get_payload_from_data(CCTM::$data);
		$save_me = json_encode($save_me);
		
		$data['msg'] = sprintf('<div class="updated"><p>%s</p></div>'
			, sprintf(__('Your Custom Content Type definition %s should begin downloading shortly.  If there is a problem downloading, you can copy the text below into a text editor and save it with a <code>.cctm.json</code> extension.', CCTM_TXTDOMAIN)
			, '<strong>'.CCTM_ImportExport::get_download_title($sanitized['title']).'</strong>')
		);
		$data['msg'] .= "<textarea rows='10' cols='100'>$save_me</textarea>";

		// Save the options: anything that's in the form is considered a valid "info" key.
		self::$data['export_info'] = $sanitized;
		update_option(self::db_key, self::$data);

		// Fire off a request to download the file:
		//$data['msg'] .= sprintf('<script type="text/javascript" src="%s"></script>', CCTM_URL.'/js/download_def.js');
		$data['msg'] .= sprintf('<script type="text/javascript">window.location="%s"</script>', CCTM_URL.'/ajax-controllers/download_def.php');
	}
	elseif($_POST['export_type'] == 'to_library') {
		// Save the options: anything that's in the form is considered a valid "info" key.
		self::$data['export_info'] = $sanitized;
		update_option(self::db_key, self::$data);
		
		if( CCTM_ImportExport::export_to_local_webserver() ) {
			$data['msg'] = sprintf('<div class="updated"><p>%s</p></div>'
					, __('Your Custom Content Type definition has been saved to your library. <a href="?page=cctm_tools&a=import_def">Click here</a> to view your library.', CCTM_TXTDOMAIN)
				);
		}
		else {
			$data['msg'] = CCTM::format_errors();
		}
	}
}

// Populate the values
$data['title'] = CCTM::get_value(self::$data['export_info'], 'title');
$data['author'] = CCTM::get_value(self::$data['export_info'], 'author');
$data['url'] = CCTM::get_value(self::$data['export_info'], 'url');
$data['description'] = CCTM::get_value(self::$data['export_info'], 'description');
$data['template_url'] = CCTM::get_value(self::$data['export_info'], 'template_url');

$data['content'] = CCTM::load_view('export.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/