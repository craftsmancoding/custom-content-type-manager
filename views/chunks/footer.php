<?php if (!defined('CCTM_PATH')) die('No direct script access allowed'); ?>

	<div id="cctm_footer">
		<p style="margin:10px;">
			<span class="cctm-link">
				<div class="fb-like" data-href="https://www.facebook.com/CustomContentTypeManager" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div>
			</span>
			<span class="cctm-link">
				<a href="?page=cctm&a=help"><img class="cctm-img" src="<?php print CCTM_URL; ?>/images/help.png" height="32" width="32" alt="help"/></a>
				<a href="http://craftsmancoding.com/products/downloads/support/"><?php _e('Get Professional Help!', CCTM_TXTDOMAIN); ?></a>
			</span>
			<span class="cctm-link">
				<a href="<?php print \CCTM\Route::url('tools/bugreport'); ?>"><img class="cctm-img" src="<?php print CCTM_URL; ?>/images/space-invader.png" height="32" width="32" alt="bug"/></a>
				<a href="<?php print \CCTM\Route::url('tools/bugreport'); ?>"><?php _e('Report a Bug', CCTM_TXTDOMAIN); ?></a></span>
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