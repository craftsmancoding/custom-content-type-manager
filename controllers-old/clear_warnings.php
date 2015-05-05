<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Marks all warning messages as read.
------------------------------------------------------------------------------*/
$nonce = self::get_value($_GET, '_wpnonce');
if (! wp_verify_nonce($nonce, 'cctm_clear_warnings') ) {
	die( __('Invalid request.', CCTM_TXTDOMAIN ) );
}

if (isset(self::$data['warnings'])) {
	foreach(self::$data['warnings'] as $warning => $viewed) {
		if ($viewed == 0) {
			self::$data['warnings'][$warning] = time(); // not 0 = read. (timestamped)
		}
	}
	update_option(self::db_key, self::$data);
	$msg = '<div class="updated"><p>'
		. __('All warnings have been dismissed.', CCTM_TXTDOMAIN)
		. '</p></div>';
	self::set_flash($msg);
	print '<script type="text/javascript">window.location.replace("?page=cctm");</script>';
	return;
}

/*EOF*/