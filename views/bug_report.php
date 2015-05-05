<p><?php _e('I am sorry that you are having trouble with this plugin, but thank you for taking the time to file a bug. It helps out everybody who uses this code. Your input is valuable! Click the link above to launch the bug tracker.', CCTM_TXTDOMAIN); ?></p>
	
<h3><?php _e('System Info', CCTM_TXTDOMAIN); ?></h3>
<p><?php _e('Please paste the following text into your bug report so I can better diagnose the problem you are experiencing.', CCTM_TXTDOMAIN); ?></p>
	
<textarea rows="20" cols="60" class="sample_code_textarea" style="border: 1px solid black;">
*SYSTEM INFO* <?php print "\n"; ?>
------------------------ <?php print "\n"; ?>
Plugin Version: <?php print CCTM::version; print '-'; print CCTM::version_meta; print "\n"; ?>
WordPress Version: <?php global $wp_version; print $wp_version; print "\n";?>
PHP Version: <?php print phpversion(); print "\n"; ?>
MySQL Version: <?php 
global $wpdb;
$result = $wpdb->get_results( 'SELECT VERSION() as ver' );
print $result[0]->ver;
print "\n";
?>
Server OS: <?php print PHP_OS; print "\n"; ?>
Language: <?php print WPLANG; print "\n"; ?>
------------------------ <?php print "\n"; ?>
ACTIVE PLUGINS: <?php 
print "\n";
$active_plugins = get_option('active_plugins'); 
$all_plugins = get_plugins();
foreach ($active_plugins as $plugin) {
//	print_r($all_plugins[$plugin]);
	if ( $all_plugins[$plugin]['Name'] != 'Custom Content Type Manager' ) {
		printf (' * %s v.%s [%s]'
			, $all_plugins[$plugin]['Name']
			, $all_plugins[$plugin]['Version']
			, $all_plugins[$plugin]['PluginURI']
		);
		print "\n";
	}
}
?>
------------------------ <?php print "\n"; 
$theme_data = wp_get_theme();
//die(print_r($theme_data,true));
?>
CURRENT THEME: <?php printf('%s v.%s %s', $theme_data->Name, $theme_data->Version, $theme_data->get('ThemeURI') ); ?>
</textarea>

<p><?php _e('When reporting bugs, remember the following key points:', CCTM_TXTDOMAIN); ?></p>

<ol>
	<li><?php _e("<strong>If the bug can't be reproduced, it can't be fixed.</strong> Provide <em>detailed</em> instructions so that someone else can make the plugin fail for themselves.", CCTM_TXTDOMAIN); ?></li>
	<li><?php _e("<strong>Be ready to provide extra information if the programmer needs it.</strong> If they didn't need it, they wouldn't be asking for it.", CCTM_TXTDOMAIN); ?></li>
	<li><?php _e("<strong>Write clearly.</strong> Make sure what you write can't be misinterpreted. Avoid pronouns, and error on the side of providing too much information instead of too little.", CCTM_TXTDOMAIN); ?></li>
</ol>
<p><?php _e('Consider using <a href="http://www.techsmith.com/jing/free/">Jing</a> to do a screencast of the problem!', CCTM_TXTDOMAIN); ?></p>
<p><?php _e('The gist of this was inspired by <a href="http://www.chiark.greenend.org.uk/~sgtatham/bugs.html">How to Report Bugs Effectively</a> by Simon Tatham.', CCTM_TXTDOMAIN);?></p>