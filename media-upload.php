<?php
/**
 * We use this intermediary file to customize the tabs shown on the media upload
 * thickbox.  It's a cumbersome workaround.... ugh. Wordpress I hate you so much.
 *
 * The process is this:
 *
 * 1. We include the CCTM class so we can set variables that are "global"-ish
 * 2. We set the $hide_url_tab to true
 * 3. When we include the /wp-admin/media-upload.php file, this triggers the action
 *      'media_upload_tabs' (see the loader.php):
 *      add_filter('media_upload_tabs', 'CCTM::customize_upload_tabs'); 
 *      The CCTM::customize_upload_tabs() function is run, and the "from url" tab
 *      gets hidden.  
 * 4. We then rest the CCTM value so that we don't hide the tab from built-in WP 
 *      fields.
 */

require_once dirname(__FILE__).'/includes/CCTM.php';

CCTM::$hide_url_tab =  true;

// We hold WP's hand because its dev's failed to validate their vars (see wp-includes/vars.php) 
$_SERVER['PHP_SELF'] = '/wp-admin/media-upload.php'; 
include dirname(dirname(dirname(dirname(__FILE__)))).'/wp-admin/media-upload.php';

CCTM::$hide_url_tab =  false;
/*EOF*/