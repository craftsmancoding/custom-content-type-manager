<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#cctm_instance_wrapper_[+id+]').sortable();
	});
	// counts iterations of multiple instances
	if (cctm["[+id+]"] == undefined) {
		cctm["[+id+]"] = '[+i+]';
	}
</script>

<div class="cctm_element_wrapper" id="custom_field_[+id+]">
	<label for="[+id+]" class="cctm_label cctm_[+type+]" id="cctm_label_[+id+]">[+label+]</label><br/>
	
	<span class="button" onclick="javascript:add_field_instance('[+id+]');">[+add_label+]</span>
	<span class="button" onclick="javascript:remove_all_relations('[+id+]');">Remove All</span>

	<br/><br/>
	<div id="cctm_instance_wrapper_[+id+]">
		[+content+]
	</div>
	[+error_msg+]
	<p class="cctm_description">[+description+]</p>
</div>