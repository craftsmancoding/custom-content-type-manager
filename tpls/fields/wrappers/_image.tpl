<div class="cctm_element_wrapper" id="custom_field_[+id+]">
	<label for="[+id+]" class="cctm_label cctm_[+type+]" id="cctm_label_[+id+]">[+label+]</label><br/>
	
	<span class="button" onclick="javascript:thickbox_results('[+id+]','[+type+]');">[+button_label+]</span>
	<span class="button" onclick="javascript:cctm_upload('[+id+]','[+type+]','replace');">Upload</span>	
	
	<!-- target is used for optional thickbox content -->
	<div id="target_[+id+]"></div>
	<div id="cctm_instance_wrapper_[+id+]" class="cctm_instance_wrapper">
		[+content+]
	</div> 
	[+error_msg+]
	<p class="cctm_description">[+description+]</p>
</div>

