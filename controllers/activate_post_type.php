<?php 
/*------------------------------------------------------------------------------
Activating a post type will cause it to show up in the WP menus and its custom
fields will be managed.
------------------------------------------------------------------------------*/
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
require_once(CCTM_PATH.'/includes/CCTM_PostTypeDef.php');

// Validate post type
if (!CCTM_PostTypeDef::is_existing_post_type($post_type) ) {
	include(CCTM_PATH.'/controllers/error.php');
	return;
}

$is_foreign = (int) CCTM::get_value($_GET, 'f');
if ($is_foreign) {
	self::$data['post_type_defs'][$post_type]['is_active'] = 2;
}
else {
	self::$data['post_type_defs'][$post_type]['is_active'] = 1;
}

update_option( self::db_key, self::$data );
$msg = '
		<div class="updated">
			<p>'
	. sprintf( __('The %s post_type has been activated.', CCTM_TXTDOMAIN), '<em>'.$post_type.'</em>')
	. '</p>
		</div>';
self::set_flash($msg);

// Bonus: because the menus are drawn before we ever get here, we refresh the page via Javascript 
// to ensure that active post types are added to menus. In other words, we sorta do a double-page load:
// WP has already sent headers, so we can't use PHP to redirect, so we use the browser to redirect.
$msg = '
	<script type="text/javascript">
		window.location.replace("?page=cctm");
	</script>';
print $msg;

/*EOF*/