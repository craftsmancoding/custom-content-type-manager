<?php
/**
 * This handles loading of dictionaries/lexicons for i18n/locale settings.  If you 
 * have added your own modifications to this plugin or if you want to customize the 
 * lexicon in any way (e.g. by loading your own translations for custom add-ons)
 * then , you can save a modified version of this file here:
 *
 * 		wp-content/uploads/cctm/config/lang/dictionaries.php
 *
 * Any file at the above location will override this file.
 */

load_plugin_textdomain( CCTM_TXTDOMAIN, false, basename(CCTM_PATH).'/lang/' );

/*EOF*/