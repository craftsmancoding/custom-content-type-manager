<span class="cctm_date_wrapper cctm_sortable" id="instance_[+id_prefix+][+id+][+i+]">
	<script>
		jQuery(function() {
			jQuery("#[+id_prefix+][+id+][+i+]").datepicker({
				dateFormat : "[+date_format+]"
			});
		});
	</script>

	<input type="text" name="[+name_prefix+][+name+][]" class="cctm_date [+class+]" id="[+id_prefix+][+id+][+i+]"  value="[+value+]" [+extra+]/>
	<span class="cctm_text cctm_close_rollover [+class+]" onclick="javascript:remove_html('instance_[+id_prefix+][+id+][+i+]');"></span><br/>
</span>