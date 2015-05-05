<?php if (!defined('CCTM_PATH')) die('No direct script access allowed'); ?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=436889603042486";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="wrap">

	<?php /*---------------- HEADER and TABS --------------------------- */ ?>
	<div id="cctm_header">
		<img src="<?php print CCTM_URL; ?>/images/cctm-logo.png" alt="custom-content-type-manager-logo"  style="float:left; margin-right:5px;"/>
		<p class="cctm_header_text">Custom Content Type Manager <span class="cctm_version">[<?php print \CCTM\CCTM::get_current_version(); ?>]</span>
			<a href="<?php print $data->help; ?>" target="_new" title="Contextual Help" style="text-decoration: none;">
				<img src="<?php print CCTM_URL; ?>/images/question-mark.gif" width="16" height="16" />
			</a>
		<br/>
		<span class="cctm_page_title"><?php print $data->pagetitle; ?></span>
		</p>
	</div>

	<div id="cctm_mainmenu">
		<a href="<?php print \CCTM\Route::url('posttypes/index'); ?>" class="cctm_tab<?php print $data->tab_posttypes; ?>"><?php _e('Content Types', CCTM_TXTDOMAIN); ?></a>
		<a href="<?php print \CCTM\Route::url('customfields/index'); ?>" class="cctm_tab<?php print $data->tab_customfields; ?> ?>"><?php _e('Custom Fields', CCTM_TXTDOMAIN); ?></a>
		<a href="<?php print \CCTM\Route::url('settings/index'); ?>" class="cctm_tab<?php print $data->tab_settings; ?>"><?php _e('Global Settings', CCTM_TXTDOMAIN); ?></a>
		<a href="<?php print \CCTM\Route::url('posttypes/index'); ?>" class="cctm_tab<?php print $data->tab_tools; ?>"><?php _e('Tools', CCTM_TXTDOMAIN); ?></a>
		<a href="<?php print \CCTM\Route::url('posttypes/index'); ?>" class="cctm_tab<?php print $data->tab_tools; ?>"><?php _e('Info', CCTM_TXTDOMAIN); ?></a>
	</div>

	<?php 
	/* Any Message (e.g. notices and errors) */
	print $data->msg; 
	?>