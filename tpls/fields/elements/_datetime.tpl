<script>
	jQuery(function() {
		jQuery("#[+id_prefix+][+id+]").datetimepicker({
			dateFormat : "[+date_format+]"
		});
	});
</script>

<input type="text" name="[+name_prefix+][+name+]" class="cctm_date [+class+]" id="[+id_prefix+][+id+]"  value="[+value+]" [+extra+]/>
