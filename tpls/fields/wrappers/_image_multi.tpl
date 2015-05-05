<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#cctm_instance_wrapper_[+id+]').sortable();
	});
</script>

<div class="cctm_element_wrapper" id="custom_field_[+id+]">
	<label for="[+id+]" class="cctm_label cctm_[+type+]" id="cctm_label_[+id+]">[+label+]</label><br/>
	
	<span class="button" onclick="javascript:thickbox_results('[+id+]','[+type+]',true);">[+button_label+]</span>
	<span class="button" onclick="javascript:remove_all_relations('[+id+]');">Remove All</span>
	<span class="button" onclick="javascript:cctm_upload('[+id+]','[+type+]','append');">Upload</span>
	<!-- target is where the thickbox will be generated -->
	<div id="target_[+id+]"></div>
	<div id="cctm_instance_wrapper_[+id+]" class="cctm_instance_wrapper">
		[+content+]
	</div> 
	[+error_msg+]
	<p class="cctm_description">[+description+]</p>
</div>