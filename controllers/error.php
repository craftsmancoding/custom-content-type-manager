<?php
/*------------------------------------------------------------------------------
Returned on errors. Future: accept an argument identifying an error

@param string $msg_id identifies the error.
------------------------------------------------------------------------------*/
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');

$msg = '';
switch ($msg_id) {
	case 'invalid_field_name':
		$msg = '<p>'. __('Invalid field name.', CCTM_TXTDOMAIN)
			. '</p><a class="button" href="?page=cctm_fields">'. __('Back', CCTM_TXTDOMAIN). '</a>';
		break;
	case 'invalid_metabox_id':
		$msg = '<p>'. __('Invalid Metabox ID.', CCTM_TXTDOMAIN)
			. '</p><a class="button" href="?page=cctm">'. __('Back', CCTM_TXTDOMAIN). '</a>';
		break;
	case 'no_cttm_def_available':
		$msg = '<p>'. __('There is no definition that is ready for importing, or the definition that you are trying to import is empty.', CCTM_TXTDOMAIN)
			. '</p><a class="button" href="?page=cctm">'. __('Import Definition', CCTM_TXTDOMAIN). '</a>';
		break;
	case 'invalid_warning_id':
		$msg = '<p>'. __('Invalid warning.', CCTM_TXTDOMAIN)
			. '</p><a class="button" href="?page=cctm">'. __('Back', CCTM_TXTDOMAIN). '</a>';		
		break;	
	default:
		$msg = '<p>'. __('Invalid post type.', CCTM_TXTDOMAIN)
			. '</p><a class="button" href="?page=cctm">'. __('Back', CCTM_TXTDOMAIN). '</a>';
}
wp_die( $msg );

/*EOF*/