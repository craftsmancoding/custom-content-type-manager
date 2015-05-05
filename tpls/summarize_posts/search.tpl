<style>
[+css+]
</style>
<p>[+widget_desc+] <a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/SearchParameters"><img src="[+cctm_url+]/images/question-mark.gif" width="16" height="16" /></a></p>
<form id="search_parameters_form" class="[+form_name+]">
	[+content+]


	<div id="filter_on_field_target">
	</div>
	
	<select id="field_selector">
		<option value="post_title">[+post_title_label+]</option>
		<option value="post_author">[+author_id_label+]</option>
		[+custom_fields+]
	</select>
	<span class="button" onclick="javascript:generate_field_filter('field_selector','filter_on_field_target');">[+add_filter_label+]</span>
	<br/>
	<hr/>
	<br/>
	<span class="button" onclick="javascript:generate_shortcode('search_parameters_form');">[+generate_shortcode_label+]</span>
	<span class="button" onclick="javascript:tb_remove();">[+cancel_label+]</span>
</form>