<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Export a content type definition to a .json file
------------------------------------------------------------------------------*/
require_once(CCTM_PATH . '/includes/CCTM_ImportExport.php');

$data 				= array();
$data['page_title']	= __('Import Definition', CCTM_TXTDOMAIN);
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Import';
$data['menu'] 		= sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_tools&a=tools" title="%s" class="button">%s</a>', __('Back'), __('Back')) . ' '.
						sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_tools&a=export_def" title="%s" class="button">%s</a>',__('Export'), __('Export'));
$data['msg']		= CCTM::get_flash();
$data['content'] = '';
$data['defs_array'] = array();

// We reference this in a couple places.
$upload_dir = wp_upload_dir();
$dir = $upload_dir['basedir'] .'/'.self::base_storage_dir . '/' . self::def_dir;

// Check to see if the library directory exists...
if ( file_exists($dir) && is_dir($dir) ) {
//	$data['msg'] = ''; // do nothing
	// Read the files
	$data['defs_array'] = CCTM_ImportExport::get_defs();
	
} 
elseif (!@mkdir($dir, self::new_dir_perms, true)) {
	$data['msg'] = sprintf('<div class="error"><p>%s</p></div>'
		, sprintf(__('Failed to create the CCTM base storage directory: <code>%s</code></p>
		<p><a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Permissions" target="_blank">Click here</a> for more information about correcting permissions errors on your server.</p>', $dir))
	);
	$data['defs_array'] = array();
}


// We have up-to 3 forms on this page....
if ( !empty($_POST) ) { // && check_admin_referer($data['action_name'], $data['nonce_name']) ) {

		// We use the 'cctm_nonce' field to determine which form was submitted.
		$nonce = CCTM::get_value($_POST, 'cctm_nonce');
		
		// If properly submitted, Proceed with importing
		if (wp_verify_nonce($nonce, 'cctm_upload_def') ) {

			// A little cleanup before we sanitize
			unset($_POST['cctm_nonce']);
			unset($_POST['_wp_http_referer']);

			// Start Checking stuff....
			// Big no-no #1: no file 
			if ( empty($_FILES) || empty($_FILES['cctm_settings_file']['tmp_name'])) {
				self::$errors['cctm_settings_file'] = sprintf( 
					__('No file selected', CCTM_TXTDOMAIN)
					, CCTM::max_def_file_size 
				); 
				$data['msg'] = self::format_errors();
				$data['content'] = CCTM::load_view('import.php', $data);
				print CCTM::load_view('templates/default.php', $data);
				return;
			}
			// Big no-no #2: file is too  big
			if ($_FILES['cctm_settings_file']['size'] > CCTM::max_def_file_size ) {
				self::$errors['cctm_settings_file'] = sprintf( 
					__('The definition filesize must not exceed %s bytes.', CCTM_TXTDOMAIN)
					, CCTM::max_def_file_size 
				); 
				$data['msg'] = self::format_errors();
				$data['content'] = CCTM::load_view('import.php', $data);
				print CCTM::load_view('templates/default.php', $data);
				return;
			}
			
			// Big no-no #3: bad data structure
			$raw_file_contents = file_get_contents($_FILES['cctm_settings_file']['tmp_name']);
			$data_from_file = json_decode( $raw_file_contents, true);

			// Let's check that this thing is legit
			if ( !CCTM_ImportExport::is_valid_def_structure($data_from_file) ) {
				self::$errors['format'] = __('The uploaded file is not in the correct format.', CCTM_TXTDOMAIN);
				$data['msg'] = self::format_errors();
				$data['content'] = CCTM::load_view('import.php', $data);
				print CCTM::load_view('templates/default.php', $data);
				return;	
			}
			
			// create_verify_storage_directories will set errors, and we add another error here
			// to let the user know that we can't interface with the library dir 
			$basename = basename($_FILES['cctm_settings_file']['name']);
			// Sometimes you can get filenames that look lie "your_def.cctm (1).json"
			if ( !CCTM_ImportExport::is_valid_basename($basename) ) {
				// grab anything left of the first period, then re-create the .cctm.json extension
				list($basename) = explode('.', $basename);
				$basename .= CCTM_ImportExport::extension;
			}

			if ( !@move_uploaded_file($_FILES['cctm_settings_file']['tmp_name'], $dir.'/'.$basename )) {
				self::$errors['library'] = sprintf( 
					__('We could not upload the definition file to your library. This may be due to permissions errors or some other server configuration.  Use FTP to upload your file to %', CCTM_TXTDOMAIN)
					, "<code>$dir/$basename</code>");	
			}
		
			// Any other errors?
			if ( !empty(self::$errors) ) {
				$data['msg'] = self::format_errors();
				self::set_flash($data['msg']);
			}

			// Refresh the list of files
			print '<script type="text/javascript">window.location.replace("?page=cctm_tools&a=import_def");</script>';
			return;
		}
		// Delete definitions
		elseif (wp_verify_nonce($nonce, 'cctm_delete_defs')) {
			$defs = CCTM::get_value($_POST, 'defs', array());
			if (CCTM_ImportExport::delete_defs($defs)) {
				$data['msg'] = sprintf('<div class="updated"><p>%s</p></div>'
					, __('Files have been removed from your library.', CCTM_TXTDOMAIN)
				);
				CCTM::set_flash($data['msg']);
				print '<script type="text/javascript">window.location.replace("?page=cctm_tools&a=import_def");</script>';
				return;
			}
			// problems deleting
			else {
				$data['msg'] = CCTM::format_errors();
			}
		}
		// Activate the previewed definition
		elseif(wp_verify_nonce($nonce, 'cctm_activate_def')) {
			if (CCTM_ImportExport::activate_def($_POST['def'])) {
				$data['msg'] = sprintf('<div class="updated"><p>%s</p></div>'
					, __('The definition was imported successfully!', CCTM_TXTDOMAIN)
				);
				CCTM::set_flash($data['msg']);
				print '<script type="text/javascript">window.location.replace("?page=cctm_tools&a=import_def");</script>';
				return;
			}
			else {
				$data['msg'] = CCTM::format_errors();
			}
			
		}
		else {
			$data['msg'] = __('Invalid submission.', CCTM_TXTDOMAIN);
		}

}

$data['content'] = CCTM::load_view('import.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/