<h3 class="cctm_subheading">single-<?php print $data['post_type'] ?>.php</h3>
<p>
	<?php print $data['single_page_msg']; ?>
</p>
<br />

<textarea cols="80" rows="10" class="sample_code_textarea" style="border: 1px solid black;"><?php print $data['single_page_sample_code']; ?></textarea>

<h3 class="cctm_subheading"><?php _e('CSS for Manager Pages', CCTM_TXTDOMAIN); ?></h3>
	<p>All of the form fields have plenty of CSS hooks so you can customize the way the manager displays for any particular content type. Add any overrides you want to your theme's <code>editor-style.css</code> file.</p>

<h3 class="cctm_subheading"><?php _e('HTML for Manager Pages', CCTM_TXTDOMAIN); ?></h3>
	<p>See the wiki page on <a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/CustomizingManagerHTML">Customizing Manager HTML</a>.</p>
