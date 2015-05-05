<?php if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
/*------------------------------------------------------------------------------
Template used for CCTM manager pages. This page should be included by the
controllers with the following variables set:

$data['page_title']:	Header of this Admin page
$data['msg']:			Any message (e.g. after form submission)
$data['menu']:			Navigation links (e.g. back, cancel, etc)
$data['content']:		Main content block
------------------------------------------------------------------------------*/
// CSS classes
$active = array();
$active['cctm']   = '';
$active['cctm_fields'] = '';
$active['cctm_metaboxes'] = '';
$active['cctm_settings'] = '';
$active['cctm_themes'] = '';
$active['cctm_tools'] = '';
$active['cctm_info'] = '';

$page = CCTM::get_value($_GET, 'page');
$active[$page] = ' cctm_active'; // active tab class

// for custom menu items, not registered via WP
$a = CCTM::get_value($_GET, 'a');
if ( $a == 'info') {
	$active['cctm_info'] = ' cctm_active';
	$active['cctm'] = '';
}
// Default help page:
if (!isset($data['help']) || empty($data['help'])) {
	$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/';
}
?>
<div class="wrap">

	<?php /*---------------- HEADER and TABS --------------------------- */ ?>
	<div id="cctm_header">
		<img src="<?php print CCTM_URL; ?>/images/cctm-logo.jpg" alt="custom-content-type-manager-logo" width="88" height="55" style="float:left; margin-right:20px;"/>
		<p class="cctm_header_text">Custom Content Type Manager <span class="cctm_version">[<?php print CCTM::get_current_version(); ?>]</span>
			<a href="<?php print $data['help']; ?>" target="_new" title="Contextual Help" style="text-decoration: none;">
				<img src="<?php print CCTM_URL; ?>/images/question-mark.gif" width="16" height="16" />
			</a>
		<br/>
		<span class="cctm_page_title"><?php print $data['page_title']; ?></span>
		</p>
	</div>

	<div id="cctm_mainmenu">
		<a href="<?php print get_admin_url(false, 'admin.php'); ?>?page=cctm" class="cctm_tab<?php print $active['cctm']; ?>"><?php _e('Content Types', CCTM_TXTDOMAIN); ?></a>
		<a href="<?php print get_admin_url(false, 'admin.php'); ?>?page=cctm_fields" class="cctm_tab<?php print $active['cctm_fields']; ?>"><?php _e('Custom Fields', CCTM_TXTDOMAIN); ?></a>
		<a href="<?php print get_admin_url(false, 'admin.php'); ?>?page=cctm_settings" class="cctm_tab<?php print $active['cctm_settings']; ?>"><?php _e('Global Settings', CCTM_TXTDOMAIN); ?></a>
		<!-- a href="<?php ?>?page=cctm_themes" class="cctm_tab<?php print $active['cctm_themes']; ?>"><?php _e('Themes', CCTM_TXTDOMAIN); ?></a -->
		<a href="<?php print get_admin_url(false, 'admin.php'); ?>?page=cctm_tools" class="cctm_tab<?php print $active['cctm_tools']; ?>"><?php _e('Tools', CCTM_TXTDOMAIN); ?></a>
		<a href="<?php print get_admin_url(false, 'admin.php'); ?>?page=cctm&a=info" class="cctm_tab<?php print $active['cctm_info']; ?>"><?php _e('Info', CCTM_TXTDOMAIN); ?></a>
	</div>

	<?php 
	/* Any Message (e.g. notices and errors) */
	print $data['msg']; 
	?>

	<div id="cctm_nav"><?php print $data['menu']; ?></div>

	<?php 
	/* ----------------- MAIN PAGE CONTENT -------------------------------*/
	print $data['content']; 
	/* -------------------------------------------------------------------*/
	?>

	<?php /*--------------- FOOTER --------------------------*/ ?>
	<div id="cctm_footer">
		<p style="margin:10px;">
			<span class="cctm-link">
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FABHDKPU7P6LN" target="_blank"><img class="cctm-img" src="<?php print CCTM_URL; ?>/images/heart.png" height="32" width="32" alt="heart"/></a>
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FABHDKPU7P6LN" target="_blank"><?php _e('Support this Plugin', CCTM_TXTDOMAIN); ?></a>
			</span>
			<span class="cctm-link">
				<a href="?page=cctm&a=help"><img class="cctm-img" src="<?php print CCTM_URL; ?>/images/help.png" height="32" width="32" alt="help"/></a>
				<a href="?page=cctm&a=help"><?php _e('Help', CCTM_TXTDOMAIN); ?></a>
			</span>
			<span class="cctm-link">
				<a href="?page=cctm&a=bug_report"><img class="cctm-img" src="<?php print CCTM_URL; ?>/images/space-invader.png" height="32" width="32" alt="bug"/></a>
				<a href="?page=cctm&a=bug_report"><?php _e('Report a Bug', CCTM_TXTDOMAIN); ?></a></span>
			<span class="cctm-link">
				<a href="http://eepurl.com/dlfHg" target="_blank"><img class="cctm-img" src="<?php print CCTM_URL; ?>/images/newspaper.png" height="32" width="32" alt="Newsletter"/></a>
				<a href="http://eepurl.com/dlfHg" target="_blank"><?php _e('Get eMail Updates', CCTM_TXTDOMAIN); ?></a>
			</span>
			<span class="cctm-link">
				<a href="http://wordpress.org/tags/custom-content-type-manager?forum_id=10" target="_blank"><img class="cctm-img" src="<?php print CCTM_URL; ?>/images/forum.png" height="32" width="32" alt="forum"/></a>
				<a href="http://wordpress.org/tags/custom-content-type-manager?forum_id=10" target="_blank"><?php _e('Forum', CCTM_TXTDOMAIN); ?></a>
			</span>
		</p>
	</div>

</div>